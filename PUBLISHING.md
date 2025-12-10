# Publishing Guide: Publishing to Packagist

This guide will walk you through publishing your `oscar-team/odoo-json2` package to Packagist (the main Composer repository).

## Prerequisites

1. ✅ A GitHub account (or GitLab/Bitbucket)
2. ✅ A Packagist account (free at https://packagist.org)
3. ✅ Your package code ready in a Git repository

## Step 1: Prepare Your Package

### 1.1 Update composer.json

Make sure your `composer.json` is complete and accurate:

```json
{
    "name": "oscar-team/odoo-json2",
    "description": "Modern PHP client library for Odoo JSON-2 API (Odoo 19+)",
    "type": "library",
    "keywords": [
        "odoo",
        "json-2",
        "api",
        "client",
        "erp",
        "laravel"
    ],
    "license": "MIT",
    "authors": [
        {
            "name": "Mueez Sattar Khan",
            "email": "msk@hejoscar.dk"
        }
    ],
    "require": {
        "php": "^8.2",
        "guzzlehttp/guzzle": "^7.8"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.10"
    },
    "autoload": {
        "psr-4": {
            "OdooJson2\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "OdooJson2\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "test-coverage": "phpunit --coverage-html coverage",
        "analyse": "phpstan analyse src"
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

**Important points:**
- ✅ Package name follows `vendor/package` format
- ✅ Description is clear and descriptive
- ✅ License is specified (MIT)
- ✅ Authors are listed
- ✅ PHP version requirement is correct (^8.2)
- ✅ `minimum-stability` and `prefer-stable` are set

### 1.2 Verify Required Files

Ensure you have these files in your repository:

- ✅ `composer.json` - Package definition
- ✅ `README.md` - Documentation
- ✅ `LICENSE` - License file (MIT)
- ✅ `.gitignore` - Excludes vendor/, etc.
- ✅ `phpunit.xml` - Test configuration
- ✅ `src/` - Source code
- ✅ `tests/` - Test files

### 1.3 Update README Installation Instructions

Update the installation section in your README:

```markdown
## Installation

```bash
composer require oscar-team/odoo-json2
```
```

## Step 2: Set Up Git Repository

### 2.1 Initialize Git (if not already done)

```bash
# Check if git is initialized
git status

# If not, initialize
git init
```

### 2.2 Create .gitignore (if not exists)

```bash
# .gitignore
/vendor/
/.phpunit.cache/
/coverage/
composer.lock
.idea/
.vscode/
*.swp
*.swo
*~
.DS_Store
```

### 2.3 Commit All Files

```bash
# Add all files
git add .

# Create initial commit
git commit -m "Initial release: Odoo JSON-2 PHP client library"
```

### 2.4 Create GitHub Repository

1. Go to https://github.com/new
2. Repository name: `odoo-json2` (or your preferred name)
3. Description: "Modern PHP client library for Odoo JSON-2 API (Odoo 19+)"
4. Choose Public or Private
5. **Don't** initialize with README, .gitignore, or license (you already have them)
6. Click "Create repository"

### 2.5 Push to GitHub

```bash
# Add remote (replace with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/odoo-json2.git

# Or if using SSH
git remote add origin git@github.com:YOUR_USERNAME/odoo-json2.git

# Push to GitHub
git branch -M main
git push -u origin main
```

## Step 3: Create Your First Release (Tag)

Packagist requires tags for versioning. Create your first release:

### 3.1 Create Version Tag

```bash
# For version 1.0.0
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0

# Or create and push in one command
git tag v1.0.0 && git push origin v1.0.0
```

**Version naming conventions:**
- Use semantic versioning: `MAJOR.MINOR.PATCH` (e.g., 1.0.0, 1.0.1, 1.1.0, 2.0.0)
- Tag format: `v1.0.0` or `1.0.0` (both work)
- For pre-releases: `v1.0.0-alpha`, `v1.0.0-beta`, `v1.0.0-rc1`

### 3.2 Create GitHub Release (Optional but Recommended)

1. Go to your repository on GitHub
2. Click "Releases" → "Create a new release"
3. Choose tag: `v1.0.0`
4. Release title: `v1.0.0`
5. Description: Add release notes
6. Click "Publish release"

## Step 4: Submit to Packagist

### 4.1 Create Packagist Account

1. Go to https://packagist.org
2. Click "Sign up" or "Log in"
3. Sign in with GitHub (recommended) or create account

### 4.2 Submit Your Package

1. Click "Submit" in the top menu
2. Enter your repository URL: `https://github.com/YOUR_USERNAME/odoo-json2`
3. Click "Check"
4. Review the package information
5. Click "Submit"

### 4.3 Verify Package

1. Your package should appear at: `https://packagist.org/packages/oscar-team/odoo-json2`
2. Wait a few minutes for Packagist to index your package
3. Test installation:

```bash
# In a new project
composer require oscar-team/odoo-json2
```

## Step 5: Set Up Auto-Update (Recommended)

Packagist can automatically update when you push new tags. Set this up:

### 5.1 Get Packagist API Token

1. Go to https://packagist.org/profile/
2. Click "Show API Token"
3. Copy your token

### 5.2 Add GitHub Webhook

1. Go to your GitHub repository
2. Settings → Webhooks → Add webhook
3. Payload URL: `https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME`
4. Content type: `application/json`
5. Secret: Your Packagist API token
6. Events: Select "Just the push event"
7. Active: ✅
8. Click "Add webhook"

### 5.3 Add Packagist Service (Alternative)

1. Go to your GitHub repository
2. Settings → Integrations → Add service
3. Search for "Packagist"
4. Enter your Packagist username and API token
5. Save

## Step 6: Versioning Strategy

### 6.1 Semantic Versioning

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.0.0 → 2.0.0): Breaking changes
- **MINOR** (1.0.0 → 1.1.0): New features, backward compatible
- **PATCH** (1.0.0 → 1.0.1): Bug fixes, backward compatible

### 6.2 Creating New Versions

```bash
# After making changes and committing
git add .
git commit -m "Add new feature X"

# Create new tag
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0

# Packagist will auto-update (if webhook is set up)
```

### 6.3 Pre-Release Versions

For alpha/beta releases:

```bash
git tag -a v1.0.0-alpha -m "Alpha release"
git tag -a v1.0.0-beta.1 -m "Beta 1 release"
git tag -a v1.0.0-rc.1 -m "Release candidate 1"
```

Users can install pre-releases with:

```bash
composer require oscar-team/odoo-json2:^1.0@alpha
```

## Step 7: Best Practices

### 7.1 Keep composer.json Updated

- Update version constraints as dependencies change
- Keep PHP version requirement current
- Update description if features change

### 7.2 Maintain CHANGELOG.md

Create a `CHANGELOG.md` file:

```markdown
# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2024-01-15

### Added
- Initial release
- Support for Odoo JSON-2 API
- Eloquent-like models
- Full CRUD operations

## [1.1.0] - 2024-02-01

### Added
- Multi-connection support
- New query methods

### Fixed
- Bug in relationship hydration
```

### 7.3 Write Good Release Notes

When creating GitHub releases, include:
- What's new
- What's changed
- What's fixed
- Breaking changes (if any)
- Migration guide (if needed)

### 7.4 Test Before Releasing

```bash
# Run tests
composer test

# Check for issues
composer validate

# Test installation in clean environment
cd /tmp
composer create-project test-project
cd test-project
composer require oscar-team/odoo-json2
```

## Step 8: Maintenance

### 8.1 Respond to Issues

- Monitor GitHub issues
- Respond to questions
- Fix bugs promptly
- Consider feature requests

### 8.2 Security Updates

- Keep dependencies updated
- Monitor security advisories
- Release security patches quickly

### 8.3 Documentation

- Keep README.md updated
- Add examples for new features
- Document breaking changes

## Troubleshooting

### Package Not Found After Submission

- Wait 5-10 minutes for indexing
- Check repository URL is correct
- Verify tag exists and is pushed
- Check Packagist logs

### Auto-Update Not Working

- Verify webhook is active
- Check webhook delivery logs on GitHub
- Verify API token is correct
- Try manual update on Packagist

### Version Not Showing

- Ensure tag is pushed: `git push origin v1.0.0`
- Check tag format (v1.0.0 or 1.0.0)
- Verify composer.json is valid: `composer validate`

## Quick Reference Commands

```bash
# Validate composer.json
composer validate

# Create and push tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0

# Update package manually on Packagist
# Go to: https://packagist.org/packages/oscar-team/odoo-json2
# Click "Update" button

# Test installation
composer require oscar-team/odoo-json2

# Check package info
composer show oscar-team/odoo-json2
```

## Next Steps After Publishing

1. ✅ Share on social media
2. ✅ Add to awesome-php lists (if applicable)
3. ✅ Write blog post (optional)
4. ✅ Monitor usage and feedback
5. ✅ Plan next features

## Resources

- [Packagist Documentation](https://packagist.org/about)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Semantic Versioning](https://semver.org/)
- [GitHub Releases Guide](https://docs.github.com/en/repositories/releasing-projects-on-github)

---

**Congratulations!** Once published, your package will be available to millions of PHP developers worldwide! 🎉

