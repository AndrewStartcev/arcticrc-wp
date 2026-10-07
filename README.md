# ArcticRC WordPress Theme

Кастомная WordPress-тема нового сайта ГК «Центр Арктических Изысканий».

Исходная HTML/BEM-верстка и ассеты находятся в `html/` и `assets/`. WordPress-интеграция выполняется без визуального конструктора и без переписывания существующей frontend-структуры.

## Базовый стек

- WordPress
- PHP
- ACF Pro + Local JSON
- Rank Math SEO
- существующие CSS / JavaScript / media assets

## Архитектура

- `service` + `service_direction`
- `project`
- `equipment` + `equipment_mode`
- глобальная ACF options page `ArcticRC`

Полный рабочий план: `docs/WORDPRESS-INTEGRATION.md`.
