 # Movie Database Platform Implementation Plan

## 1. Project Initialization & Core Setup

### Install Laravel & Essential Packages

- Install fresh Laravel 11 project in `/www/wwwroot/omdbapibt.prus.dev`
- Install core packages:
- `laravel/cashier` - Stripe subscription management
- `barryvdh/laravel-debugbar` - Development debugging
- `laravel/horizon` - Queue management dashboard
- `spatie/laravel-translatable` - Multilingual content support
- `livewire/livewire` - Full-stack reactive components
- `livewire/flux` - Official Livewire UI components

### Database & Environment Configuration

- Configure MariaDB connection optimized for movie data
- Set up environment variables for APIs (OMDb, TMDb keys)
- Configure Stripe keys for subscription processing

## 2. Database Schema Design

### Core Tables (php4dvd-inspired + extended fields)

**Movies Table:**

- id, tmdb_id, imdb_id, omdb_id, title, original_title, year, runtime, release_date
- plot, tagline, homepage, budget, revenue, status, popularity, vote_average, vote_count
- poster_path, backdrop_path, trailer_url, media_type, adult, video
- timestamps, soft deletes

**TV Shows Table:**

- id, tmdb_id, imdb_id, name, original_name, first_air_date, last_air_date
- number_of_seasons, number_of_episodes, episode_run_time, status
- overview, tagline, homepage, popularity, vote_average, vote_count
- poster_path, backdrop_path, media_type, adult
- timestamps, soft deletes

**People Table (Actors/Directors):**

- id, tmdb_id, imdb_id, name, biography, birthday, deathday, place_of_birth
- gender, known_for_department, popularity, profile_path
- timestamps

**Genres Table:** id, name, slug, tmdb_id

**Countries Table:** id, name, code, active

**Languages Table:** id, name, code, native_name, active

**Pivot Tables:**

- movie_genre, tv_show_genre, movie_language, tv_show_language
- movie_country, tv_show_country, movie_person (role), tv_show_person (role)

### Multilingual Content (Spatie Translatable)

- Use JSON translation columns: `title`, `overview`, `biography` fields
- Store translations in format: `{'en': 'English', 'es': 'Español', 'fr': 'Français'}`

### Subscription Tables (Laravel Cashier)

- users, subscriptions, subscription_items (auto-created by Cashier)
- Add `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at` to users table

## 3. API Integration & Data Parsing

### Multi-Source Parser Service

Create `MovieDataParserService` combining:

- **OMDb API** - Primary IMDb data source (paid tier for unlimited)
- **TMDb API** - Comprehensive movie/TV/person data + multilingual metadata
- **Additional APIs** - JustWatch (streaming), TheAudioDB (soundtracks)

### Parser Features

- Scheduled artisan commands: `movie:parse-new`, `tv:parse-new`, `people:parse-new`
- Chunk processing (25 items) to prevent timeouts
- Hash-based deduplication using MD5(tmdb_id + imdb_id)
- Language-specific content fetching (loop all active languages)
- Queue-based background processing via Horizon
- Error handling with retry logic (3 attempts)

### Data Flow

1. Fetch from TMDb (free, comprehensive)
2. Enrich with OMDb (IMDb ratings, additional metadata)
3. Process multilingual content for all active languages
4. Store with translations in JSON columns
5. Update pivot relationships (genres, countries, languages, cast)

## 4. Authentication & Subscription System

### User Authentication

- Use Laravel Breeze with Livewire stack (pure Livewire, no Volt)
- Roles: guest, free_user, subscriber, admin
- Middleware: `CheckSubscription` for premium content

### Stripe Integration (Laravel Cashier)

- Subscription plans: 
- Free (browse only, limited details)
- Premium Monthly ($9.99) - full access
- Premium Yearly ($99.99) - full access + bonus features
- Features:
- Stripe Checkout for subscription creation
- Webhook handling: `customer.subscription.created`, `customer.subscription.updated`, `customer.subscription.deleted`
- Billing portal for users to manage subscriptions
- Trial period: 7 days free trial for new users

### Access Control

- Free users: Browse movies/shows, view basic info only
- Subscribers: Full details, cast/crew, streaming links, download watch history
- Implement via gates and policies

## 5. Livewire Components (Pure Livewire Architecture)

### Public Components

- `MovieList` - Paginated grid with filters (genre, year, rating)
- `MovieDetail` - Full movie info with tabs (overview, cast, reviews)
- `TvShowList` - TV shows grid with filters
- `TvShowDetail` - Show details with seasons/episodes
- `PersonDetail` - Actor/director biography with filmography
- `SearchGlobal` - Unified search across movies/TV/people

### User Dashboard Components

- `SubscriptionManage` - View plan, cancel/resume, billing portal
- `WatchHistory` - User's viewing history (subscribers only)
- `Watchlist` - Saved movies/shows
- `Recommendations` - Personalized suggestions

### Admin Components

- `ParserManage` - Trigger parsers, view queue status
- `ContentModerate` - Edit/approve parsed content
- `UserManage` - View users, subscriptions
- `AnalyticsDashboard` - Revenue, user stats

## 6. Multilingual Implementation

### Spatie Translatable Setup

- Configure supported languages in `config/translatable.php`
- Use middleware to detect language from:

1. URL prefix (`/es/movies`)
2. User preference (stored in session/DB)
3. Browser accept-language header
4. Fallback to English

### Translation Strategy

- **UI translations:** Laravel lang files (`resources/lang/{locale}/`)
- **Content translations:** JSON columns in models
- Auto-fetch translations from TMDb API during parsing
- Admin interface to add missing translations manually

### URL Structure

- `/{locale}/movies` - Movies listing
- `/{locale}/movies/{slug}` - Movie detail
- `/{locale}/tv/{slug}` - TV show detail
- `/{locale}/people/{slug}` - Person detail

## 7. Design & UI (Flux UI + Custom)

### Design System

- Flux UI components for forms, buttons, modals, navigation
- Custom Tailwind CSS theme inspired by php4dvd but modernized:
- Dark mode support (toggle in navbar)
- Netflix-style card layouts for content
- IMDb-inspired rating displays
- Custom movie posters grid with hover effects

### Key Pages

- **Homepage:** Hero section + trending movies/shows
- **Browse:** Advanced filters (genre, year, rating, language, country)
- **Detail Pages:** Comprehensive movie/show info matching php4dvd fields:
- Poster, title, year, runtime, genres, countries, languages
- Director, cast, plot, ratings, trailer embed
- Streaming availability, watch providers
- User ratings, reviews (future feature)
- **Pricing:** Subscription plans comparison table
- **Account:** Profile, subscription, watch history, preferences

### Responsive Design

- Mobile-first approach
- Flux sidebar component for mobile navigation
- Lazy loading images with placeholders
- Infinite scroll for listings

## 8. Performance Optimizations

### Caching Strategy

- **Never cache parsed data** (per user preference from memory)
- Cache API responses: 24 hours for static content (genres, languages)
- Cache expensive queries: trending movies (1 hour), popular shows (1 hour)
- Use Redis for session storage

### Database Optimization

- Indexes on: tmdb_id, imdb_id, slug, title, popularity, vote_average
- Full-text search indexes on title, overview columns
- Hash columns for fast deduplication (MD5 of composite keys)

### Queue Management

- Horizon dashboard at `/horizon`
- Separate queues: `parsing`, `default`, `emails`
- Retry failed jobs: 3 attempts with exponential backoff

## 9. Security & Compliance

### Data Protection

- Rate limiting on API endpoints
- CSRF protection (Laravel default)
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade escaping)

### API Terms Compliance

- TMDb: Display attribution logo, link to TMDb
- OMDb: Respect rate limits, use paid tier
- Stripe: PCI compliance via Stripe Elements/Checkout

## 10. Testing & Deployment

### Testing Strategy

- Feature tests for parsers, subscriptions
- Unit tests for payment logic, access control
- Browser tests for critical flows (signup, subscribe, browse)

### Deployment Checklist

- Run migrations: `php artisan migrate --force`
- Seed initial data: languages, countries, genres
- Configure queue worker: `php artisan horizon`
- Environment-specific configs (production API keys)

### Local Development URL

- Project accessible at: `https://omdbapibt.prus.dev` 