<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

    'reset' => [
        'public' => 'TRUNCATE ONLY 
                        public.rooms,
                        public.notifications,
                        public.files,
                        public.audit_logs,
                        public.personal_access_tokens,
                        public.failed_jobs,
                        public.password_resets
                    RESTART IDENTITY CASCADE;',

        'academic' => 'TRUNCATE ONLY 
                        academic.memorize_cards,
                        academic.audit_exam_scores, 
                        academic.column_students,
                        academic.student_alumnis,
                        academic.student_dept_histories,
                        academic.student_mutations,
                        academic.student_class_histories,
                        academic.exam_report_comments,
                        academic.exam_report_comment_socials,
                        academic.exam_report_comment_lessons,
                        academic.exam_report_score_finals,
                        academic.exam_report_score_infos,
                        academic.exam_reports,
                        academic.exam_score_final_weights,
                        academic.exam_score_final_weight_infos,
                        academic.exam_score_finals,
                        academic.avg_score_classes,
                        academic.avg_score_students,
                        academic.exam_scores,
                        academic.exams,
                        academic.presence_lesson_student_presents,
                        academic.presence_lesson_students,
                        academic.presence_lessons,
                        academic.presence_daily_students,
                        academic.presence_dailies,
                        academic.calendar_activities,
                        academic.calendars,
                        academic.lesson_schedules,
                        academic.lesson_schedule_teachings,
                        academic.lesson_schedule_infos,
                        academic.lesson_schedule_times,
                        academic.lesson_assessments,
                        academic.teachers,
                        academic.lesson_gradings,
                        academic.lesson_exams,
                        academic.lesson_plans,
                        academic.lessons,
                        academic.lesson_groups,
                        academic.score_aspects,
                        academic.admission_configs,
                        academic.column_prospect_students,
                        academic.column_options,
                        academic.columns,
                        academic.students,
                        academic.prospect_students,
                        academic.prospect_student_groups,
                        academic.admissions,
                        academic.classes,
                        academic.semesters,
                        academic.schoolyears,
                        academic.grades
                    RESTART IDENTITY CASCADE;',

        'finance' => 'TRUNCATE ONLY 
                        finance.journal_vouchers, 
                        finance.expenditure_details,
                        finance.audit_code_balances,
                        finance.begin_balances,
                        finance.audit_journal_details,
                        finance.audit_journals,
                        finance.audit_savings,
                        finance.audit_expenditures,
                        finance.audit_receipt_others,
                        finance.audit_receipt_voluntaries,
                        finance.audit_receipt_majors,
                        finance.audit_payment_majors,
                        finance.audits,
                        finance.savings,
                        finance.saving_types,
                        finance.expenditures,
                        finance.requested_users,
                        finance.expenditure_types,
                        finance.receipt_categories,
                        finance.receipt_others,
                        finance.receipt_voluntaries,
                        finance.receipt_majors,
                        finance.payment_majors,
                        finance.journal_details,
                        finance.journals,
                        finance.receipt_types,
                        finance.book_years
                    RESTART IDENTITY;', 

    ]

];
