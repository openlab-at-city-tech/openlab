# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

StellarWP Arrays is a PHP library providing 50+ array manipulation utilities for WordPress plugins and PHP projects. It's designed as a standalone library that can be integrated into any PHP 7.4+ project.

## Commands

### Testing
```bash
# Run tests with SLIC
slic use arrays
slic run wpunit

# Run specific test
slic run wpunit tests/wpunit/GetTest.php
```

### Static Analysis
```bash
# Run PHPStan static analysis (level 5)
composer test:analysis
```

### Documentation
```bash
# Generate API documentation
composer create-docs
```

### Development Setup
```bash
# Install dependencies
composer install

# The arrays.php file is a WordPress plugin bootstrap for testing only
# Actual library code is in src/Arrays/Arr.php
```

## Architecture

### Core Structure
- **Main Class**: `StellarWP\Arrays\Arr` in `src/Arrays/Arr.php` - Contains all array manipulation methods
- **Namespace**: `StellarWP\Arrays` (PSR-4 autoloaded)
- **Methods**: 50+ static methods for array manipulation (get, set, filter, map, etc.)

### Testing Architecture
- **Framework**: Codeception with WP Browser for WordPress integration
- **Test Suite**: `wpunit` - WordPress unit tests
- **Test Location**: `tests/wpunit/` - Each method has its own test file
- **Base Class**: Tests extend `ArraysTestCase` for common functionality
- **CI**: Uses SLIC (StellarWP's infrastructure CLI) for automated testing

### Key Design Patterns
1. **Static Methods**: All array utilities are static methods on the `Arr` class
2. **Dot Notation**: Many methods support dot notation for nested array access (e.g., 'user.profile.name')
3. **Fluent Interface**: Methods can be chained for complex operations
4. **Laravel-inspired**: Many methods ported from Laravel's array helpers

### Integration Notes
- Can be used standalone via Composer: `composer require stellarwp/arrays`
- For production use in plugins, use Strauss for namespace prefixing to avoid conflicts
- The `arrays.php` file is only for testing - not needed in production

### Development Considerations
- No JavaScript/frontend components - pure PHP library
- WordPress coding standards should be followed
- All public methods must have comprehensive PHPDoc blocks
- New methods require corresponding test files in `tests/wpunit/`