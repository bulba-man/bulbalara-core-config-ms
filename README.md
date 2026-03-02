# Config MS (MoonShine)

Пакет добавляет кастомные конфиги в базу данных и интегрирует их в config laravel. Изменение конфигов на отдельной странице.


## Требования
- PHP 8.2+
- Laravel 12+
- MoonShine 4+

## Установка
```bash
composer require bulbalara/config-ms
php artisan config-ms:install
```

Опционально опубликуйте конфиг:
```bash
php artisan vendor:publish --provider="Bulbalara\CoreConfigMs\ConfigMsServiceProvider"
```

## Конфигурация
Файл: `config/config_ms.php`
```php
return [
    'database' => [
        'connection' => '',
        'core_config_table' => 'core_config_admin',
    ],
    'urls' => [
        'settings_uri' => 'settings',
        'settings_config_uri' => 'config',
    ],
    'pages' => [
        'settings' => \Bulbalara\CoreConfigMs\Moonshine\Pages\ConfigPage::class,
    ],
    'add_to_menu' => true,
];
```

## Как устроен `path`
Поле `path` определяет таб/группу/поле:
```
{tab}.{group}.{field}
```
Пример:
```
general.general.site_name
```
Это создаст:
- таб `general`
- группу `general`
- поле `site_name`

## Типы полей (`backend_type`)
Используется `backend_type`.
Поддерживаются:
- `text`, `textarea`, `email`, `url`, `password`
- `number`, `switcher`/`checkbox`/`boolean`
- `date`, `time`, `color`, `range`
- `select`, `list`, `json`
- `file`, `image`
- так же FQCN (Fully Qualified Class Name) поля MoonShine (если реализует `FieldContract`)

### Учет `cast`
Если `cast`:
- `bool`/`boolean` → `Switcher`
- `int`/`integer`/`float`/`decimal` → `Number`
- `json` → `Json::object()`

#### Примеры `cast`
```
path: contacts.general.phone
cast: string
```
Рендерится как `Text`.

```
path: design.header.logo_width
cast: integer
```
Рендерится как `Number`.

```
path: design.footer.show
cast: boolean
```
Рендерится как `Switcher`.


### Учет `rules`
Если `backend_type = text`, то по `rules`:
- `email` → `Email`
- `url` → `Url`

## Методы полей (`methods`)
Поле `methods` хранит методы для поля в JSON.

### Формат хранения (БД)
```json
{
  "required": null,
  "hint": "Подсказка",
  "link": {
    "link": "https://cutcode.dev",
    "name": "CutCode",
    "blank": true
  }
}
```

### Рендер
В форме для поля будут вызваны эти методы:
```
Text::make($label, $path)
    ->required()
    ->hint('Подсказка')
    ->link(link: 'https://cutcode.dev', name: 'CutCode', blank: true)
```

на самом деле методы будут вызваны так:
```
if (is_array($arguments)) {
    $result = $field->{$method}(...$arguments);
} elseif ($arguments === null) {
    $result = $field->{$method}();
} else {
    $result = $field->{$method}($arguments);
}
```

## Зависимости полей (`depends_of`, `depends_val`)
Зависимости позволяют показывать/скрывать поле в форме конфигурации в зависимости от значения другого поля.

Используются поля `depends_of` и `depends_val`:
```
depends_of: mail.transport.mailer
depends_val: smtp
```
Это означает: текущее поле будет активно/видимо только если поле
`mail.transport.mailer` равно `smtp`.

### Пример зависимости (SMTP поля)
```
path: mail.smtp.host
depends_of: mail.transport.mailer
depends_val: smtp
```
Поле `mail.smtp.host` будет показано только если выбран `smtp`.

### Пример зависимости для boolean
```
path: design.header.logo_alt
depends_of: design.header.use_logo
depends_val: true
```

### Пример зависимости для массива значений
```
path: seo.defaults.title_prefix
depends_of: seo.defaults.mode
depends_val: ["custom","advanced"]
```

## Структура таблиц (кратко)
### `core_config`
Хранит фактическое значение и тип:
```
path: string (уникальный ключ)
value: mixed
default: mixed
cast: string|null
```

### `core_config_admin`
Хранит UI‑настройки:
```
core_config_id: int
backend_type: string
label: string|null
description: string|null
rules: string|null
order: int|null
source: json|null
depends_of: string|null
depends_val: mixed|null
methods: json|null
```

## CRUD управление конфигами
Кнопка **«Управление»** на странице конфигурации ведёт в ресурс MoonShine:
- Index: список конфигов
- Create/Edit: создание и редактирование

## Примеры

### Пример записи в `core_config`
```
path: general.general.site_name
value: "My Site"
default: ""
cast: string
```

### Пример записи в `core_config_admin`
```
backend_type: text
label: bl.config::core_config.config.general.general.site_name
description: bl.config::core_config.config.general.general.site_name_help
rules: required|string
order: 10
methods: {"required":null}
```

### Пример `select`
```
backend_type: select
source: {"smtp":"SMTP","log":"Log"}
```

### Пример `source` с группировкой
```
backend_type: select
source: {
  "SMTP": {"smtp":"SMTP", "mailgun":"Mailgun"},
  "Логи": {"log":"Log"}
}
```

### Пример `methods` для select с условием
Формат БД:
```json
{
    "options": {"smtp":"SMTP","log":"Log"},
    "showWhen": {"field":"mail.transport.mailer","value":"smtp"}
}
```

### Пример `methods` для hint и required
Формат БД:
```json
{
    "required": null,
    "hint": "Введите название сайта"
}
```

### Пример `methods` с именованными аргументами
Формат БД:
```json
{
    "sortable": null,
    "default": {"value":"log"},
    "placeholder": {"value":"Выберите транспорт"}
}
```

## Локализация
Переводы лежат в `resources/lang`, ключи: `bl.config::core_config.*`.
