# OMDb API BT Platform - Features

This document provides a comprehensive overview of all features available in the OMDb API BT Platform.

## Table of Contents

- [Core Features](#core-features)
- [User Features](#user-features)
- [Admin Features](#admin-features)
- [API Features](#api-features)
- [OMDB API Key Management](#omdb-api-key-management)
- [Subscription & Billing](#subscription--billing)
- [Social Features](#social-features)
- [Technical Features](#technical-features)

---

## Core Features

### Movie & TV Show Catalog

- **Multi-source Metadata Aggregation**
  - Integration with OMDb API
  - Integration with TMDb (The Movie Database) API
  - Automatic metadata enrichment and updates
  - Deduplication using hash-based system

- **Content Organization**
  - Movies with comprehensive metadata
  - TV Shows with episode tracking
  - Genre classification and filtering
  - Language and country associations
  - Platform availability tracking (streaming services)
  - Tag-based organization with weighted tags

- **Advanced Search & Browse**
  - Full-text search capabilities
  - Filter by genre, year, rating, language
  - Platform-specific content discovery
  - Popularity-based sorting
  - Vote average filtering

### Content Details

- **Rich Metadata**
  - Title (with multilingual support)
  - Overview and plot summaries
  - Release dates and runtime
  - Budget and revenue information
  - Vote averages and counts
  - Poster and backdrop images
  - Trailer URLs and streaming links

- **Credits System**
  - Cast information
  - Crew details
  - People profiles with filmography
  - Character and job information

---

## User Features

### Authentication & Profile

- **User Management**
  - Email/password authentication
  - Profile management
  - Preferred locale selection
  - User roles (User, Admin)
  - Account dashboard

### Content Interaction

- **Ratings & Reviews**
  - Movie rating system
  - Like/Dislike functionality
  - Written reviews with timestamps
  - Personal rating history

- **Lists Management**
  - Custom movie lists
  - Public/private list visibility
  - "Watch Later" default list
  - List descriptions and cover images
  - Positioned items within lists
  - List sharing capabilities

- **Watch History** (Premium)
  - Track viewed content
  - Watch progress tracking
  - Completion percentage
  - Watch date recording
  - Polymorphic support (movies & TV shows)

### Recommendations

- **Intelligent Recommendation Engine**
  - Genre-based preferences
  - Language alignment
  - Favorite collaborator tracking
  - Regional availability consideration
  - Watch history analysis
  - Profile-based scoring
  - Cached recommendations for performance

### User Profiles

- **Extended Profile Information**
  - Genre preferences with rankings
  - Language preferences with weightings
  - Favorite people (actors, directors)
  - Country preferences
  - Behavioral metrics
    - Weekly watch minutes
    - Session watch minutes
    - Binge scores
    - Rewatch scores
  - Recent highlights tracking

---

## Admin Features

### Admin Dashboard

- **Control Panel**
  - System overview
  - User management interface
  - Analytics dashboard
  - Horizon monitoring integration

### User Management

- **User Directory**
  - Browse all users
  - User role management
  - User impersonation
  - Management log tracking
  - Audit trail for admin actions

### Content Moderation

- **Parser Moderation Dashboard**
  - Review parsed content
  - Approve/reject entries
  - Monitor parser entry history
  - Quality control workflows

### Analytics

- **Analytics Dashboard**
  - User metrics
  - Content statistics
  - Subscription analytics
  - System performance metrics

### UI Management

- **UI Translation Manager**
  - Manage interface translations
  - Support multiple languages
  - Translation keys management
  - Fallback locale configuration

### System Monitoring

- **Horizon Monitor**
  - Queue status monitoring
  - Job failure tracking
  - Throughput metrics
  - Worker management

---

## API Features

### Public APIs

- **Movie Lookup API**
  - Search movies by IMDB ID or title
  - Rate-limited endpoint
  - No authentication required
  - Throttle: `movie-lookup` rate limit

### Administrative APIs

- **Parser Trigger API**
  - Trigger movie parsing jobs
  - Throttle: `parser-trigger` rate limit
  - Authentication required

- **OMDB Keys Import API**
  - Bulk import OMDB API keys
  - Authentication required
  - Validation and processing

---

## OMDB API Key Management

### Automated Key Discovery System

- **Key Generation**
  - Random 8-character alphanumeric key generation
  - Batch generation (default: 1,000 keys per batch)
  - Duplicate prevention using `insertOrIgnore`
  - Configurable character set and key length
  - Minimum pending keys threshold (default: 10,000)

- **Asynchronous Validation**
  - Concurrent HTTP request validation (50 keys simultaneously)
  - Laravel HTTP pool integration
  - Status tracking: pending, valid, invalid, unknown
  - Response code logging
  - Checkpoint-based resume capability
  - Progress tracking and reporting

- **Movie Parsing with Valid Keys**
  - Round-robin key rotation
  - Automatic metadata updates
  - Rate limit compliance
  - Up to 1,000 movies per run
  - Error handling and retry logic

### Key Management Commands

- **`php artisan omdb:bruteforce`**
  - All-in-one command for key management
  - Three-phase workflow: Generation → Validation → Parsing
  - Resume from interruptions
  - Real-time progress bars
  - Comprehensive status reporting

### Configuration

- **Environment Variables**
  - `OMDB_API_KEY` - Primary OMDB API key
  - `TMDB_API_KEY` - TMDb API key
  - Configurable in `config/services.php`

- **Bruteforce Settings**
  - Character set customization
  - Key length configuration
  - Batch sizes for generation and validation
  - Timeout settings
  - Minimum pending keys threshold

---

## Subscription & Billing

### Subscription Plans

- **Laravel Cashier Integration**
  - Stripe payment processing
  - Monthly and yearly plans
  - Subscription trial periods
  - Grace period handling
  - Subscription status tracking

### Billing Features

- **Customer Portal**
  - Stripe customer portal integration
  - Subscription management
  - Payment method updates
  - Invoice history
  - Billing information management

- **Subscription Gating**
  - Premium content access control
  - Subscription requirement checks
  - Trial period support
  - Grace period access
  - Content metadata-based gating

### Webhooks

- **Stripe Webhook Handler**
  - Secure webhook signature verification
  - Automatic subscription updates
  - Payment event processing
  - Failed payment handling

### Payment Tracking

- **Subscription Payments**
  - Payment history logging
  - Amount and currency tracking
  - Payment status monitoring
  - Historical payment records

---

## Social Features

### Following System

- **User Connections**
  - Follow/unfollow users
  - Followers list
  - Following list
  - Pivot table with timestamps
  - Custom Follow model for additional features

### Interactions

- **User Interactions Tracking**
  - Like/unlike actions
  - Favorite/unfavorite
  - Interaction timestamps
  - User engagement metrics

---

## Technical Features

### Architecture

- **Laravel 12**
  - Latest Laravel features
  - Streamlined application structure
  - Modern middleware handling
  - Horizon queue management

- **Database**
  - SQLite support (testing)
  - MySQL/MariaDB (production)
  - Optimized indexing
  - Foreign key constraints
  - Full-text search indexes
  - Soft deletes on key models

### Caching & Performance

- **Redis Integration**
  - Queue management
  - Cache storage
  - Session storage
  - Horizon backend

- **Caching Strategy**
  - Recommendation caching with version keys
  - Configuration caching
  - Route caching
  - View caching
  - Query result caching

### Queue System

- **Laravel Horizon**
  - Real-time queue monitoring
  - Failed job management
  - Job retry handling
  - Metrics and insights
  - Queue supervision

- **Background Jobs**
  - Movie parsing jobs
  - Metadata enrichment
  - OMDB key validation
  - Email notifications
  - Webhook processing

### Internationalization

- **Multi-language Support**
  - Laravel Translatable integration
  - Locale-based routing
  - Fallback locale handling
  - URL localization
  - UI translation system
  - Database field translations

### Frontend

- **Livewire 3**
  - Real-time component updates
  - Interactive UI without complex JavaScript
  - Component-based architecture
  - Wire:model binding
  - Form validation

- **Livewire Flux**
  - Pre-built UI components
  - Consistent design system
  - Responsive components
  - Accessible interfaces

- **Vite**
  - Modern asset bundling
  - Hot module replacement
  - Optimized production builds
  - Asset versioning

- **Tailwind CSS**
  - Utility-first styling
  - Responsive design
  - Customizable design system
  - Dark mode support

### Code Quality

- **Testing**
  - PHPUnit test suite
  - Feature tests
  - Unit tests
  - Database factories
  - Test-driven development
  - Comprehensive test coverage

- **Laravel Pint**
  - Automatic code formatting
  - PSR-12 compliance
  - Consistent code style
  - Pre-commit formatting

- **Debugging**
  - Laravel Debugbar integration
  - Laravel Pail for log monitoring
  - Detailed error pages
  - Query logging (development)

### Security

- **Authentication & Authorization**
  - Secure password hashing
  - Remember me functionality
  - CSRF protection
  - Role-based access control
  - Admin middleware
  - Subscriber middleware

- **Payment Security**
  - Stripe webhook signature verification
  - Secure API key storage
  - Environment-based secrets
  - PCI compliance via Stripe

### API Documentation

- **Scribe Integration**
  - Automatic API documentation
  - Interactive API explorer
  - Code examples
  - Authentication documentation

### Development Tools

- **Composer Scripts**
  - `composer dev` - Start all services concurrently
  - `composer setup` - Complete project setup
  - `composer test` - Run test suite
  - Automated deployment scripts

- **Artisan Commands**
  - Custom OMDB bruteforce command
  - Database seeders
  - Cache management
  - Queue workers

### Logging & Monitoring

- **Audit Logs**
  - Admin action tracking
  - User management logs
  - System event logging
  - Comprehensive audit trails

- **Parser History**
  - Track all parsing operations
  - Entry history for content
  - Success/failure tracking
  - Performance metrics

### Data Models

**Core Models:**
- Movie
- TvShow
- Person
- Genre
- Language
- Country
- Platform
- Tag

**User Models:**
- User
- UserProfile
- UserManagementLog
- AdminAuditLog

**Interaction Models:**
- Rating
- Review
- ListModel
- ListItem
- Follow
- Interaction
- WatchHistory

**System Models:**
- OmdbApiKey
- OmdbApiKeyProgress
- ParserEntry
- ParserEntryHistory
- UiTranslation

**Billing Models:**
- Subscription (via Cashier)
- SubscriptionPayment

**Pivot Models:**
- FilmTagPivot
- ListMoviePivot
- MoviePlatformPivot

---

## Deployment

### Production Features

- **Automated Deployment Script**
  - Zero-downtime deployment
  - Asset compilation
  - Cache optimization
  - Database migrations
  - Horizon restart

- **GitHub Actions Workflow**
  - Automated deployment to production
  - SSH-based deployment
  - Manual trigger workflow
  - Environment configuration
  - Secure secrets management

### Environment Configuration

- **Required Environment Variables**
  - Database credentials
  - Redis configuration
  - Stripe API keys
  - OMDB API key
  - TMDB API key
  - Webhook secrets
  - Application settings

---

## Roadmap

### Planned Features

- Enhanced recommendation algorithms
- Machine learning integration
- Advanced analytics
- Mobile application
- Content discovery improvements
- Social features expansion
- API rate limit optimization
- Performance improvements

---

For detailed technical documentation, see:
- [README.md](README.md) - Setup and configuration
- [OMDB_BRUTEFORCE_README.md](OMDB_BRUTEFORCE_README.md) - OMDB key system
- [docs/bruteapi.md](docs/bruteapi.md) - Technical documentation
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Implementation details

**Last Updated:** October 17, 2025
**Version:** 1.0.0
**License:** MIT

