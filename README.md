
# Kobeni Laravel11 helper (˶ᵔ ᵕ ᵔ˶)

#### 1. Is this a library ? no it is not a library but a small mini framework or you can just call it a helper.

#### 2. So what does it help ? not much but can help you query some stuff more easy.



## Installation

Nah just clone this composer install php kobeni migrate and kobeni ser
    
## Features

- prebuild helper 6 array manipulation methods
- prebuild authentication logics 
- prebuild helper one device login 
- prebuild helper log all users log into devices
- prebuild helper terminate all devices and also specific
- prebuild helper custom database query ready to be use
- prebuild helper strong password validation actually this will coming for mores so stay tunes

## Usage/Examples

```bash
php kobeni make:kobeniController ExampleController

php kobeni make:after MethodName

php kobeni make:before MethodName

php kobeni make:staticRp ClassName

php kobeni make:dynamicRp className
```

```php

public function example()
{
    try {
        $data = $this->findAll->allDataWithSelect([
            'data' => User::class,
            'sort' => ['created_at', 'desc'],
            'select' => ['id', 'name', 'email', 'created_at'],
            'relations' => [
                'posts' => function ($query) {
                    $query->select('id', 'title', 'created_at');
                }
            ],
            'search' => [
                'name' => $this->req->name,
            ],
        ]);

        return $this->dataResponse($data);
    } catch (Exception $e) {
        return $this->handleException($e, $this->req);
    }
}

$user = $this->TokenRegister([
  'credentials' => $credentials,
  'model' => User::class
]);

public function getCollection(){
    $data = [
    'test' => [
        'test1' => [
          'test2' => [
            'test3' => [
              'name' => 'renko',
              'test4' => []
            ]
          ]
        ]
    ],
    'anotherTest' => [
      'test1' => [
        'test2' => [
          'test3' => [
            'name' => 'anotherName'
          ]
        ]
      ]
    ],
    'newTest' => [
      'name' => 'newName'
    ]
  ];

  $results = $this->recursivePluck($data, 'name');
        
  return $this->dataResponse($results);
}

```

