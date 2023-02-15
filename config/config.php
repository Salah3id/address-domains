<?php

use Salah3id\Domains\Activators\FileActivator;
use Salah3id\Domains\Commands;

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Namespace
    |--------------------------------------------------------------------------
    |
    | Default domain namespace.
    |
    */

    'namespace' => 'Domains',

    /*
    |--------------------------------------------------------------------------
    | Domain Stubs
    |--------------------------------------------------------------------------
    |
    | Default domain stubs.
    |
    */

    'stubs' => [
        'enabled' => false,
        'path' => base_path('vendor/salah3id/address-domains/src/Commands/stubs'),
        'files' => [
            'routes/web' => 'Routes/web.php',
            'routes/api' => 'Routes/api.php',
            'views/index' => 'Resources/views/index.blade.php',
            'views/master' => 'Resources/views/layouts/master.blade.php',
            'scaffold/config' => 'Config/config.php',
            'composer' => 'composer.json',
            'assets/js/app' => 'Resources/assets/js/app.js',
            'assets/sass/app' => 'Resources/assets/sass/app.scss',
            'vite' => 'vite.config.js',
            'package' => 'package.json',
        ],
        'replacements' => [
            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME'],
            'routes/api' => ['LOWER_NAME'],
            'vite' => ['LOWER_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'DOMAIN_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/index' => ['LOWER_NAME'],
            'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'DOMAIN_NAMESPACE',
                'PROVIDER_NAMESPACE',
            ],
        ],
        'gitkeep' => true,
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Domains path
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated domain. This path also will be added
        | automatically to list of scanned folders.
        |
        */

        'domains' => base_path('Domains'),
        /*
        |--------------------------------------------------------------------------
        | Domains assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the domains assets path.
        |
        */

        'assets' => public_path('domains'),
        /*
        |--------------------------------------------------------------------------
        | The migrations path
        |--------------------------------------------------------------------------
        |
        | Where you run 'domain:publish-migration' command, where do you publish the
        | the migration files?
        |
        */

        'migration' => base_path('database/migrations'),
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate key to false to not generate that folder
        */
        'generator' => [
            'config' => ['path' => 'Config', 'generate' => true],
            'command' => ['path' => 'Console', 'generate' => true],
            'migration' => ['path' => 'Database/Migrations', 'generate' => true],
            'seeder' => ['path' => 'Database/Seeders', 'generate' => true],
            'factory' => ['path' => 'Database/factories', 'generate' => true],
            'model' => ['path' => 'Entities', 'generate' => true],
            'routes' => ['path' => 'Routes', 'generate' => true],
            'controller' => ['path' => 'Http/Controllers', 'generate' => true],
            'filter' => ['path' => 'Http/Middleware', 'generate' => true],
            'request' => ['path' => 'Http/Requests', 'generate' => true],
            'provider' => ['path' => 'Providers', 'generate' => true],
            'assets' => ['path' => 'Resources/assets', 'generate' => true],
            'lang' => ['path' => 'Resources/lang', 'generate' => true],
            'views' => ['path' => 'Resources/views', 'generate' => true],
            'test' => ['path' => 'Tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => true],
            'repository' => ['path' => 'Repositories', 'generate' => false],
            'event' => ['path' => 'Events', 'generate' => false],
            'listener' => ['path' => 'Listeners', 'generate' => false],
            'policies' => ['path' => 'Policies', 'generate' => false],
            'rules' => ['path' => 'Rules', 'generate' => false],
            'jobs' => ['path' => 'Jobs', 'generate' => false],
            'emails' => ['path' => 'Emails', 'generate' => false],
            'notifications' => ['path' => 'Notifications', 'generate' => false],
            'resource' => ['path' => 'Transformers', 'generate' => false],
            'component-view' => ['path' => 'Resources/views/components', 'generate' => false],
            'component-class' => ['path' => 'View/Components', 'generate' => false],
        ],


    /*
    |--------------------------------------------------------------------------
    | Repositories Generator Config
    |--------------------------------------------------------------------------
    |
    */
    'repo-generator'  => [
        'stubsOverridePath' => app()->path(),
        'paths'         => [
            'models'       => 'Entities',
            'repositories' => 'Repositories/Eloquent',
            'interfaces'   => 'Repositories/Interfaces',
            'transformers' => 'Transformers',
            'presenters'   => 'Presenters',
            'validators'   => 'Validators',
            'controllers'  => 'Http/Controllers',
            'provider'     => 'RepositoryServiceProvider',
            'criteria'     => 'Criteria',
            'relations'    => 'Entities/Traits/Relations'
        ]
    ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Package commands
    |--------------------------------------------------------------------------
    |
    | Here you can define which commands will be visible and used in your
    | application. If for example you don't use some of the commands provided
    | you can simply comment them out.
    |
    */
    'commands' => [
        Commands\CommandMakeCommand::class,
        Commands\ComponentClassMakeCommand::class,
        Commands\ComponentViewMakeCommand::class,
        Commands\ControllerMakeCommand::class,
        Commands\DisableCommand::class,
        Commands\DumpCommand::class,
        Commands\EnableCommand::class,
        Commands\EventMakeCommand::class,
        Commands\JobMakeCommand::class,
        Commands\ListenerMakeCommand::class,
        Commands\MailMakeCommand::class,
        Commands\MiddlewareMakeCommand::class,
        Commands\NotificationMakeCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderMakeCommand::class,
        Commands\InstallCommand::class,
        Commands\ListCommand::class,
        Commands\DomainDeleteCommand::class,
        Commands\DomainMakeCommand::class,
        Commands\FactoryMakeCommand::class,
        Commands\PolicyMakeCommand::class,
        Commands\RequestMakeCommand::class,
        Commands\RuleMakeCommand::class,
        Commands\MigrateCommand::class,
        Commands\MigrateFreshCommand::class,
        Commands\MigrateRefreshCommand::class,
        Commands\MigrateResetCommand::class,
        Commands\MigrateRollbackCommand::class,
        Commands\MigrateStatusCommand::class,
        Commands\MigrationMakeCommand::class,
        Commands\ModelMakeCommand::class,
        Commands\PublishCommand::class,
        Commands\PublishConfigurationCommand::class,
        Commands\PublishMigrationCommand::class,
        Commands\PublishTranslationCommand::class,
        Commands\SeedCommand::class,
        Commands\SeedMakeCommand::class,
        Commands\SetupCommand::class,
        Commands\UnUseCommand::class,
        Commands\UpdateCommand::class,
        Commands\UseCommand::class,
        Commands\ResourceMakeCommand::class,
        Commands\TestMakeCommand::class,
        Commands\LaravelDomainsV6Migrator::class,

        Commands\RepositoryCommand::class,
        Commands\TransformerCommand::class,
        Commands\PresenterCommand::class,
        Commands\EntityCommand::class,
        Commands\ValidatorCommand::class,
        Commands\ControllerCommand::class,
        Commands\BindingsCommand::class,
        Commands\CriteriaCommand::class
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => 'salah3id',
        'author' => [
            'name' => 'Nicolas Widart',
            'email' => 'n.widart@gmail.com',
        ],
        'composer-output' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled' => false,
        'driver' => 'file',
        'key' => 'address-domains',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what address-domains will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
        /**
         * load files on boot or register method
         *
         * Note: boot not compatible with asgardcms
         *
         * @example boot|register
         */
        'files' => 'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    |
    | You can define new types of activators here, file, database etc. The only
    | required parameter is 'class'.
    | The file activator will store the activation status in storage/installed_domains
    */
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('domains_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Criteria Config
    |--------------------------------------------------------------------------
    |
    | Settings of request parameters names that will be used by Criteria
    |
    */
    'criteria'   => [
        /*
        |--------------------------------------------------------------------------
        | Accepted Conditions
        |--------------------------------------------------------------------------
        |
        | Conditions accepted in consultations where the Criteria
        |
        | Ex:
        |
        | 'acceptedConditions'=>['=','like']
        |
        | $query->where('foo','=','bar')
        | $query->where('foo','like','bar')
        |
        */
        'acceptedConditions' => [
            '=',
            'like',
            'in'
        ],
        /*
        |--------------------------------------------------------------------------
        | Request Params
        |--------------------------------------------------------------------------
        |
        | Request parameters that will be used to filter the query in the repository
        |
        | Params :
        |
        | - search : Searched value
        |   Ex: http://salah3id.local/?search=lorem
        |
        | - searchFields : Fields in which research should be carried out
        |   Ex:
        |    http://salah3id.local/?search=lorem&searchFields=name;email
        |    http://salah3id.local/?search=lorem&searchFields=name:like;email
        |    http://salah3id.local/?search=lorem&searchFields=name:like
        |
        | - filter : Fields that must be returned to the response object
        |   Ex:
        |   http://salah3id.local/?search=lorem&filter=id,name
        |
        | - orderBy : Order By
        |   Ex:
        |   http://salah3id.local/?search=lorem&orderBy=id
        |
        | - sortedBy : Sort
        |   Ex:
        |   http://salah3id.local/?search=lorem&orderBy=id&sortedBy=asc
        |   http://salah3id.local/?search=lorem&orderBy=id&sortedBy=desc
        |
        | - searchJoin: Specifies the search method (AND / OR), by default the
        |               application searches each parameter with OR
        |   EX:
        |   http://salah3id.local/?search=lorem&searchJoin=and
        |   http://salah3id.local/?search=lorem&searchJoin=or
        |
        */
        'params'             => [
            'search'       => 'search',
            'searchFields' => 'searchFields',
            'filter'       => 'filter',
            'orderBy'      => 'orderBy',
            'sortedBy'     => 'sortedBy',
            'with'         => 'with',
            'searchJoin'   => 'searchJoin',
            'withCount'    => 'withCount'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Cache Config
    |--------------------------------------------------------------------------
    |
    */
    'repo-cache'      => [
        /*
         |--------------------------------------------------------------------------
         | Cache Status
         |--------------------------------------------------------------------------
         |
         | Enable or disable cache
         |
         */
        'enabled'    => false,

        /*
         |--------------------------------------------------------------------------
         | Cache Minutes
         |--------------------------------------------------------------------------
         |
         | Time of expiration cache
         |
         */
        'minutes'    => 30,

        /*
         |--------------------------------------------------------------------------
         | Cache Repository
         |--------------------------------------------------------------------------
         |
         | Instance of Illuminate\Contracts\Cache\Repository
         |
         */
        'repository' => 'cache',

        /*
          |--------------------------------------------------------------------------
          | Cache Clean Listener
          |--------------------------------------------------------------------------
          |
          |
          |
          */
        'clean'      => [

            /*
              |--------------------------------------------------------------------------
              | Enable clear cache on repository changes
              |--------------------------------------------------------------------------
              |
              */
            'enabled' => true,

            /*
              |--------------------------------------------------------------------------
              | Actions in Repository
              |--------------------------------------------------------------------------
              |
              | create : Clear Cache on create Entry in repository
              | update : Clear Cache on update Entry in repository
              | delete : Clear Cache on delete Entry in repository
              |
              */
            'on'      => [
                'create' => true,
                'update' => true,
                'delete' => true,
            ]
        ],

        'params'     => [
            /*
            |--------------------------------------------------------------------------
            | Skip Cache Params
            |--------------------------------------------------------------------------
            |
            |
            | Ex: http://prettus.local/?search=lorem&skipCache=true
            |
            */
            'skipCache' => 'skipCache'
        ],

        /*
       |--------------------------------------------------------------------------
       | Methods Allowed
       |--------------------------------------------------------------------------
       |
       | methods cacheable : all, paginate, find, findByField, findWhere, getByCriteria
       |
       | Ex:
       |
       | 'only'  =>['all','paginate'],
       |
       | or
       |
       | 'except'  =>['find'],
       */
        'allowed'    => [
            'only'   => null,
            'except' => null
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Pagination Limit Default
    |--------------------------------------------------------------------------
    |
    */
    'pagination' => [
        'limit' => 15
    ],

    /*
    |--------------------------------------------------------------------------
    | Fractal Presenter Config
    |--------------------------------------------------------------------------
    |

    Available serializers:
    ArraySerializer
    DataArraySerializer
    JsonApiSerializer

    */
    'fractal'    => [
        'params'     => [
            'include' => 'include'
        ],
        'serializer' => League\Fractal\Serializer\DataArraySerializer::class
    ],
];
