<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).





I've created a reusable **Select2** component (`form-select2.blade.php`) with CDN integration. Here's what was done:

## 1. Added Select2 CDN in `resources/views/layouts/app.blade.php`
- **CSS**: `select2.min.css` added in `<head>`
- **JS**: `select2.min.js` added before sidebar JS
- **Global init**: Auto-initializes all `.select2` elements with placeholder, allowClear, and width:100%

## 2. Created `resources/views/components/form-select2.blade.php`
A full-featured reusable component like `form-select`, supporting:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | — | Label text (optional) |
| `name` | string | null | Input name attribute |
| `id` | string | null | Input ID (falls back to name) |
| `placeholder` | string | "Select an option" | Placeholder text |
| `required` | bool | false | Required field indicator |
| `multiple` | bool | false | Enable multi-select |
| `allowClear` | bool | true | Show clear button |
| `options` | array | [] | Key-value option pairs |
| `selected` | mixed | null | Selected value(s) |
| `disabled` | bool | false | Disable the select |

## Usage Examples

**Basic single select:**
```blade
<x-form-select2
    label="Department"
    name="department_id"
    :options="$departments"
    :selected="old('department_id', $user->department_id ?? null)"
/>
```

**With slot (custom options):**
```blade
<x-form-select2 label="Employee" name="employee_id">
    <option value="1">John Doe</option>
    <option value="2">Jane Smith</option>
</x-form-select2>
```

**Multi-select:**
```blade
<x-form-select2
    label="Roles"
    name="roles[]"
    :multiple="true"
    :options="$roles"
    :selected="old('roles', $userRoles ?? [])"
/>
```

**Required field:**
```blade
<x-form-select2
    label="Status"
    name="status"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
    :required="true"
/>
```

The component automatically uses `select2` class and pushes its initialization script to the `@stack('scripts')` section.
