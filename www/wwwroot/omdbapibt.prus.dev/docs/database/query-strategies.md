# Query Strategies for Movie Search

The `movies` table is optimised for the most common discovery flows in the application. The schema changes introduce dedicated indexes that cover identity lookups, popularity-based ordering and multi-lingual keyword search.

## Identity Lookups

- **TMDb / IMDb / Slug lookups** – leverage the single-column B-tree indexes on `tmdb_id`, `imdb_id` and `slug` for fast equality queries such as:
  ```sql
  select * from movies where tmdb_id = ? limit 1;
  select * from movies where imdb_id = ? limit 1;
  select * from movies where slug = ? limit 1;
  ```
  These indexes also back the unique constraints on each column to prevent duplicate records during ingestion.

- **OMDb lookups** – the existing index on `omdb_id` makes fall-back queries via the OMDb identifier efficient when TMDb/IMDb IDs are missing.

## Popularity & Rating Sorting

The `popularity` and `vote_average` numeric columns are indexed to support leaderboards and browse pages that paginate ordered result sets:

```sql
select id, title from movies order by popularity desc limit 50 offset ?;
select id, title from movies where vote_count > 100 order by vote_average desc limit 25;
```

MySQL can use the index to avoid full table scans when ordering by the indexed column.

## Full-Text Search Across Translations

Translated content is stored in JSON columns (`title`, `overview`). Generated columns (`title_search_vector`, `overview_search_vector`) flatten the English, Spanish and French values into searchable text blocks. The combined full-text index `movies_fulltext_translations` allows natural language search over both vectors:

```sql
select id, json_extract(title, '$."en"') as title
from movies
where match(title_search_vector, overview_search_vector) against (? in natural language mode)
limit 20;
```

When the query includes filters, combine them so that MySQL can apply the most selective predicate first:

```sql
select id
from movies
where match(title_search_vector, overview_search_vector) against (? in boolean mode)
  and popularity > 10
order by vote_average desc
limit 20;
```

Use *boolean mode* (e.g. `+matrix -"reloaded"`) for advanced search UI, while *natural language mode* provides relevance-ranked defaults.

## Maintenance Notes

- When adding new interface languages, update the migration (or create a follow-up migration) to append the additional `json_extract` calls inside the generated column definition so that the full-text index continues to include every locale.
- Because the generated columns are computed from JSON, no manual synchronisation is required—updates to `title`/`overview` automatically refresh the search vectors.
