# svu
Slimimum Viable UI


```tree
.
├── bin                   # Executables (CLI, scripts)
├── etc                   # Configuration files
│     └── app             ## Application configuration
│         └── conf.d      ### Environment-specific configuration
├── public                # Web accessible files/front controller
│     └── assets          ## Distribution UI assets
│           ├── css
│           ├── fonts
│           ├── images
│           └── js
├── src                   # Application source code
│     └── Database
│         └── Migrations  ### Database migrations
├── tests                 # Testing resources
│     ├── Fixture         ## Sample data for testing
│     ├── Integration     ## Request/response & database tests
│     ├── Support         ## Custom test helpers
│     └── Unit            ## Isolated unit tests 
├── usr                   # Assets/template resources
│     ├── share
│     │     └── views     ### Readonly UI templates
│     └── src             ## Source UI assets (uncompiled css/js)
│         ├── css
│         └── js
└── var                   # Variable runtime data
    ├── cache
    │     └── twig
    ├── lib               ## Database/persistent storage
    ├── log
    └── tmp
```