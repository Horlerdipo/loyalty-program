# Loyalty Program Implementation

This project is a full-stack feature for an e-commerce loyalty program where customers unlock achievements and earn badges based on their purchase history. It was built as part of a Mid-Level Full-Stack Engineer assessment.

## Overview

The loyalty program tracks user purchases and automatically unlocks relevant achievements and badges. When a user reaches specific milestones, events are fired to trigger rewards, such as cashback payments.

---

## Features

- Achievement tracking based on total number of sales.
- Achievement tracking based on total revenue generated.
- Badge progression system tied to the number of unlocked achievements.
- Automated event-driven rewards (Cashback system).
- RESTful API for fetching user loyalty progress and history.

---

## Technologies Used

- Core: PHP 8.3 / Laravel 13
- Database: SQLite
- Authentication: Laravel Sanctum
- Testing: Pest PHP
- Architecture: Action-DTO Pattern, Event-Driven Design

---

## Architecture and Design Patterns

### 1. Action-DTO Pattern
To maintain thin controllers and reusable logic, the project utilizes the Action pattern. Business logic is encapsulated within dedicated Action classes (e.g., UnlockAchievements, UnlockBadges), while Data Transfer Objects (DTOs) ensure a consistent and type-safe API response structure.

### 2. Event-Driven Architecture
The system is decoupled using Laravel's event system:
- ItemPurchased: Fired when a purchase is recorded.
- AchievementUnlocked: Fired when a user meets an achievement threshold.
- BadgeUnlocked: Fired when a user earns a new badge, triggering the cashback listener.

### 3. Background Processing
Listeners responsible for achievement and badge checks implement ShouldQueue, ensuring that the purchase flow remains fast and responsive while heavy calculations happen in the background.

---

## Setup Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL or SQLite

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/horlerdipo/loyalty-reward
   cd loyalty-program
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Environment configuration:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Database Setup:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed --class=AchievementSeeder
   php artisan db:seed --class=BadgeSeeder
   ```

5. Start the application:
   ```bash
   php artisan serve
   ```

---

## API Documentation

The project includes a Postman collection (`Loyalty Program.postman_collection.json`) for detailed endpoint testing.

### Authentication
Most endpoints require a Bearer Token obtained via the login or register endpoints.

- POST /api/auth/register: Create a new user account.
- POST /api/auth/login: Authenticate and receive an API token.

### Loyalty Endpoints
- GET /api/users/{user}/achievements: Returns the user's unlocked achievements, next available milestones, current badge, and progress toward the next badge.
- POST /api/purchase: Simulate a purchase to trigger the loyalty logic.

---

## Achievements and Badges

### Achievements
Achievements are split into two tracks:
1. Sales Track: Unlocked based on the total count of items purchased.
2. Revenue Track: Unlocked based on the total amount spent (thresholds: 1k, 5k, 10k, etc.).

### Badges
Badges are awarded based on the total number of achievements unlocked:
- Starter: 1 Achievement
- Bronze: 3 Achievements
- Silver: 6 Achievements
- Gold: 9 Achievements
- Platinum: 12 Achievements
- Sapphire: 15 Achievements
- Ruby: 18 Achievements
- Emerald: 21 Achievements
- Diamond: 24 Achievements
- Legend: 27 Achievements

### Rewards
Every time a BadgeUnlocked event is fired, a cashback of 300 Naira is automatically logged for the user and marked as paid in the database.

---

## Testing

The project uses Pest for feature and unit testing.

To run the test suite:
```bash
php artisan test
```

Key test areas include:
- Achievement unlocking logic.
- Badge progression accuracy.
- API response structure and authentication.
- Event dispatching verification.

---

## License

This project is open-sourced software licensed under the MIT license.
