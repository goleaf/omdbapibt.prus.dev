# Wiki Content for GitHub

This directory contains the markdown content for the GitHub Wiki.

## Uploading to GitHub Wiki

### Method 1: GitHub Web Interface

1. Go to your repository on GitHub
2. Click the "Wiki" tab
3. Click "Create the first page" or "New Page"
4. Copy the content from each `.md` file
5. Paste into the GitHub wiki editor
6. Save each page

### Method 2: Git Clone Wiki (Recommended)

GitHub wikis are git repositories. You can clone and push directly:

```bash
# Clone the wiki repository
git clone https://github.com/goleaf/omdbapibt.prus.dev.wiki.git

# Copy wiki files
cp wiki/*.md omdbapibt.prus.dev.wiki/

# Commit and push
cd omdbapibt.prus.dev.wiki
git add .
git commit -m "Add comprehensive wiki documentation"
git push origin master
```

### Method 3: Sync Script

Create a script to sync automatically:

```bash
#!/bin/bash
# sync-wiki.sh

WIKI_REPO="https://github.com/goleaf/omdbapibt.prus.dev.wiki.git"
TEMP_DIR=$(mktemp -d)

# Clone wiki repo
git clone "$WIKI_REPO" "$TEMP_DIR"

# Copy files
cp wiki/*.md "$TEMP_DIR/"

# Commit and push
cd "$TEMP_DIR"
git add .
git commit -m "Update wiki from main repository" || true
git push origin master

# Cleanup
rm -rf "$TEMP_DIR"

echo "Wiki synced successfully!"
```

## Wiki Pages

The following pages are included:

1. **Home.md** - Wiki home page with navigation
2. **Getting-Started.md** - Installation and setup guide
3. **OMDB-Key-Management.md** - OMDB API key system documentation
4. **API-Documentation.md** - API endpoints and usage

### Additional Pages to Create

You may want to create these additional pages:

- **User-Guide.md** - End-user documentation
- **Admin-Guide.md** - Administrative features
- **Development-Guide.md** - Development and contributing
- **Deployment-Guide.md** - Production deployment
- **Troubleshooting.md** - Common issues and solutions
- **Features-Overview.md** - Comprehensive feature list

## Page Structure

Each wiki page should:

- Start with a clear H1 heading
- Include a table of contents for long pages
- Use proper markdown formatting
- Include code examples where relevant
- Link to related pages
- Include "Last Updated" footer

## Maintenance

- Update wiki content when features change
- Keep code examples current
- Test all commands and code snippets
- Ensure links are working
- Update version numbers

## Contributing

To update wiki content:

1. Edit the `.md` files in the `wiki/` directory
2. Commit changes to the main repository
3. Sync to GitHub wiki using one of the methods above

---

**Note:** The GitHub wiki is separate from the main repository. Changes must be synced manually or via automation.

