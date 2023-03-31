define({ "api": [
  {
    "type": "get",
    "url": "/get/earnings",
    "title": "Get Partner Earnings",
    "name": "Get_Partner_Earnings",
    "description": "<p>Get Partner Earnings. Requires &quot;<strong>get_earnings</strong>&quot; API permission.</p>",
    "group": "Account",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/earnings?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/earnings\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Partner Credits\",\n  \"data\": {\n       \"earnings\": \"1.43638\",\n       \"currency\": \"GBP\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Account",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/earnings"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/credits",
    "title": "Get Remaining Credits",
    "name": "Get_Remaining_Credits",
    "description": "<p>Get Remaining Credits. Requires &quot;<strong>get_credits</strong>&quot; API permission.</p>",
    "group": "Account",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/credits?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/credits\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Remaining Credits\",\n  \"data\": {\n       \"credits\": \"798.634\",\n       \"currency\": \"GBP\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Account",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/credits"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/subscription",
    "title": "Get Subscription Package",
    "name": "Get_Subscription_Package",
    "description": "<p>Get Subscription Package. Requires &quot;<strong>get_subscription</strong>&quot; API permission.</p>",
    "group": "Account",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/subscription?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/subscription\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Subscription Package\",\n  \"data\": \"data\": {\n   \"name\": \"Starter\",\n   \"usage\": {\n           \"sms_send\": {\n               \"used\": 262,\n               \"limit\": 1000\n           },\n           \"sms_receive\": {\n               \"used\": 139,\n               \"limit\": 250\n           },\n           \"ussd\": {\n               \"used\": 16,\n               \"limit\": 0\n           },\n           \"notifications\": {\n               \"used\": 55,\n               \"limit\": 0\n           },\n           \"contacts\": {\n               \"used\": 7,\n               \"limit\": 50\n           },\n           \"devices\": {\n               \"used\": 3,\n               \"limit\": 3\n           },\n           \"apikeys\": {\n               \"used\": 4,\n               \"limit\": 5\n           },\n           \"webhooks\": {\n               \"used\": 1,\n               \"limit\": 5\n           },\n           \"actions\": {\n               \"used\": 3,\n               \"limit\": 0\n           },\n           \"scheduled\": {\n               \"used\": 0,\n               \"limit\": 0\n           },\n           \"wa_send\": {\n               \"used\": 3,\n               \"limit\": 0\n           },\n           \"wa_receive\": {\n               \"used\": 19,\n               \"limit\": 0\n           },\n           \"wa_accounts\": {\n               \"used\": 1,\n               \"limit\": 0\n           }\n       }\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Account",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/subscription"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/contact",
    "title": "Create Contact",
    "name": "Create_Contact",
    "description": "<p>Create Contact. Requires &quot;<strong>create_contact</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>phone Recipient mobile number, it will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br> <strong>Example for Philippines</strong><br> E.164: +639184661533<br> Local: 09184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of contact</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "groups",
            "description": "<p>List of contact group ID's separated by commas. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $contact = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"groups\" => \"1,2,3,4\",\n      \"phone\" => \"+639123456789\",\n      \"name\" => \"Martin Crater\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/create/contact\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $contact);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\ncontact = {\n    \"secret\": apiSecret,\n    \"groups\": \"1,2,3,4\",\n    \"phone\": \"+639123456789\",\n    \"name\": \"Martin Crater\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/create/contact\", params = contact)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Contact has been created!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/contact"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/group",
    "title": "Create Group",
    "name": "Create_Group",
    "description": "<p>Create Group. Requires &quot;<strong>create_group</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of group</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $group = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"name\" => \"Friends\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/create/group\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $group);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\ngroup = {\n    \"secret\": apiSecret,\n    \"name\": \"Friends\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/create/group\", params = group)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Contact group has been created!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/group"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/contact",
    "title": "Delete Contact",
    "name": "Delete_Contact",
    "description": "<p>Delete Contact. Requires &quot;<strong>delete_contact</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Contact ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $contactId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/contact?secret={$apiSecret}&id={$contactId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncontactId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/contact\", params = {\n    \"secret\": apiSecret\n    \"id\": contactId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Contact has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/contact"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/group",
    "title": "Delete Group",
    "name": "Delete_Group",
    "description": "<p>Delete Group. Requires &quot;<strong>delete_group</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Contact group ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $groupId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/group?secret={$apiSecret}&id={$groupId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ngroupId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/group\", params = {\n    \"secret\": apiSecret\n    \"id\": groupId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Contact group has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/group"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/unsubscribed",
    "title": "Delete Unsubscribed",
    "name": "Delete_Unsubscribed",
    "description": "<p>Delete Unsubscribed Contact. Requires &quot;<strong>delete_unsubscribed</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Contact ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $unsubscribedId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/unsubscribed?secret={$apiSecret}&id={$unsubscribedId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nunsubscribedId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/unsubscribed\", params = {\n    \"secret\": apiSecret\n    \"id\": unsubscribedId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Unsubscribed contact has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/unsubscribed"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/contacts",
    "title": "Get Contacts",
    "name": "Get_Contacts",
    "description": "<p>Get Contacts. Requires &quot;<strong>get_contacts</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/contacts?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/contacts\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Saved Contacts\",\n  \"data\": [\n       {\n           \"id\": 2,\n           \"groups\": [\n               \"1\"\n           ],\n           \"phone\": \"+639184661538\",\n           \"name\": \"Shane\"\n       },\n       {\n           \"id\": 3,\n           \"groups\": [\n               \"1\",\n               \"9\",\n               \"10\",\n               \"11\"\n           ],\n           \"phone\": \"+639206150514\",\n           \"name\": \"Terry Bom\"\n       },\n       {\n           \"id\": 4,\n           \"groups\": [\n               \"1\",\n               \"9\"\n           ],\n           \"phone\": \"+639184661532\",\n           \"name\": \"Jake Thrower\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/contacts"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/groups",
    "title": "Get Groups",
    "name": "Get_Groups",
    "description": "<p>Get Groups. Requires &quot;<strong>get_groups</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/groups?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/groups\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Contact Groups\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"name\": \"Default\"\n       },\n       {\n           \"id\": 9,\n           \"name\": \"Happy Group\"\n       },\n       {\n           \"id\": 10,\n           \"name\": \"Riders\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/groups"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/unsubscribed",
    "title": "Get Unsubscribed",
    "name": "Get_Unsubscribed",
    "description": "<p>Get Unsubscribed Contacts. Requires &quot;<strong>get_unsubscribed</strong>&quot; API permission.</p>",
    "group": "Contacts",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/unsubscribed?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/unsubscribed\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Unsubscribed Contacts\",\n  \"data\": [\n       {\n           \"id\": 2,\n           \"phone\": \"+639694967617\",\n           \"created\": 1645755138\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Contacts",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/unsubscribed"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/notification",
    "title": "Delete Notification",
    "name": "Delete_Notification",
    "description": "<p>Delete Notification. Requires &quot;<strong>delete_notification</strong>&quot; API permission.</p>",
    "group": "Notifications",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Notification ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $notificationId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/notification?secret={$apiSecret}&id={$notificationId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nnotificationId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/notification\", params = {\n    \"secret\": apiSecret\n    \"id\": notificationId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Notification has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Notifications",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/notification"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/notifications",
    "title": "Get Notifications",
    "name": "Get_Notifications",
    "description": "<p>Get Notifications. Requires &quot;<strong>get_notifications</strong>&quot; API permission.</p>",
    "group": "Notifications",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/notifications?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/notifications\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Received Notifications\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"package_name\": \"com.facebook.orca\",\n           \"title\": \"Darren Shmuck\",\n           \"content\": \"Hello World!\",\n           \"timestamp\": 1645052535\n       },\n       {\n           \"id\": 2,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"package_name\": \"com.facebook.katana\",\n           \"title\": \"Michael shared your post\",\n           \"content\": \"Michael shared your post\",\n           \"timestamp\": 1645052541\n       },\n       {\n           \"id\": 3,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"package_name\": \"com.facebook.orca\",\n           \"title\": \"Shane Blake\",\n           \"content\": \"Hello World!\",\n           \"timestamp\": 1645052543\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Notifications",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/notifications"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/otp",
    "title": "Send OTP",
    "name": "Send_OTP",
    "description": "<p>Send a one-time-password to specified mobile number. Requires &quot;<strong>otp</strong>&quot; API permission.</p>",
    "group": "OTP",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"sms\"",
              "\"whatsapp\""
            ],
            "optional": false,
            "field": "type",
            "description": "<p>Type of message, it can be SMS or WhatsApp.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>OTP message to send, you can use <strong>{{otp}}</strong> shortcode to include the otp anywhere in the message.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>Recipient mobile number, it will accept E.164 formatted numbers<br> <strong>Example for Philippines</strong><br> E.164: +639184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "expire",
            "description": "<p>OTP expiration time in seconds. This is optional, default value is 300 seconds or 5 minutes.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "account",
            "description": "<p>This is only for <strong>whatsapp</strong> type. WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"devices\"",
              "\"credits\""
            ],
            "optional": true,
            "field": "mode",
            "description": "<p>This is only required for <strong>sms</strong> type. This is the mode of sending the message, it can be &quot;devices&quot; which will allow you to use your linked android devices or &quot;credits&quot; which will allow you to use gateways and partner devices. &quot;credits&quot; requires you to have enough credit balance to send messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device",
            "description": "<p>This is only for <strong>sms</strong> type. Linked device unique ID, this is required if you will send with &quot;devices&quot; mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).</p>"
          },
          {
            "group": "Parameter",
            "type": "String|Number",
            "optional": true,
            "field": "gateway",
            "description": "<p>This is only for <strong>sms</strong> type. Partner device unique ID or gateway ID, this is required if you will send with &quot;credits&quot; mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": true,
            "field": "sim",
            "description": "<p>This is only for <strong>sms</strong> type. Sim slot number you want to use. For &quot;devices&quot; mode only.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $message = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"mode\" => \"sms\",\n      \"mode\" => \"devices\",\n      \"device\" => \"00000000-0000-0000-d57d-f30cb6a89289\",\n      \"sim\" => 1,\n      \"phone\" => \"+639123456789\",\n      \"message\" => \"Your OTP is {{otp}}\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/otp\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nmessage = {\n    \"secret\": apiSecret,\n    \"type\": \"sms\",\n    \"mode\": \"devices\",\n    \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n    \"sim\": 1,\n    \"phone\": \"+639123456789\",\n    \"message\": \"Your OTP is {{otp}}\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/otp\", params = message)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"OTP has been sent!\",\n  \"data\": {\n       phone: \"+639123456789\",\n       message: \"Your OTP is 345678\",\n       otp: 345678\n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "OTP",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/otp"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/otp",
    "title": "Verify OTP",
    "name": "Verify_OTP",
    "description": "<p>Verify one-time-password from user supplied data. Requires &quot;<strong>otp</strong>&quot; API permission.</p>",
    "group": "OTP",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "otp",
            "description": "<p>The otp you got from a user supplied input or data</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $otp = \"123456\"; // otp from a user supplied input or data\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/otp?secret={$apiSecret}&otp={$otp}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\n# otp from a user supplied input or data\notpCode = \"123456\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/otp\", params = {\n    \"secret\": apiSecret,\n    \"otp\": otpCode\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"OTP has been verified!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 403,\n  \"message\": \"OTP is invalid or expired!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "OTP",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/otp"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/sms.received",
    "title": "Delete Received Message",
    "name": "Delete_Received_Message",
    "description": "<p>Delete Received Message. Requires &quot;<strong>delete_sms_received</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Received message ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $smsId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/sms.received?secret={$apiSecret}&id={$smsId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nsmsId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/sms.received\", params = {\n    \"secret\": apiSecret\n    \"id\": smsId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Received SMS has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/sms.received"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/sms.campaign",
    "title": "Delete SMS Campaign",
    "name": "Delete_SMS_Campaign",
    "description": "<p>Delete SMS Campaign. Requires &quot;<strong>delete_sms_campaign</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/sms.campaign?secret={$apiSecret}&id={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/sms.campaign\", params = {\n    \"secret\": apiSecret\n    \"id\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"SMS campaign has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/sms.campaign"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/sms.sent",
    "title": "Delete Sent Message",
    "name": "Delete_Sent_Message",
    "description": "<p>Delete Sent Message. Requires &quot;<strong>delete_sms_sent</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Sent message ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $smsId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/sms.sent?secret={$apiSecret}&id={$smsId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nsmsId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/sms.sent\", params = {\n    \"secret\": apiSecret\n    \"id\": smsId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Sent SMS has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/sms.sent"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/devices",
    "title": "Get Devices",
    "name": "Get_Devices",
    "description": "<p>Get Devices. Requires &quot;<strong>get_devices</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/devices?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/devices\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Android Devices\",\n  \"data\": [\n       {\n           \"id\": \"49\",\n           \"unique\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"name\": \"F11 Phone\",\n           \"version\": \"Android 11\",\n           \"manufacturer\": \"OPPO\",\n           \"random_send\": false,\n           \"random_min\": 5,\n           \"random_max\": 10,\n           \"limit_status\": true,\n           \"limit_interval\": \"daily\",\n           \"limit_number\": 100,\n           \"notification_packages\": [\n               \"com.google.android.apps.messaging\",\n               \"com.facebook.orca\"\n           ],\n           \"partner\": false,\n           \"partner_sim\": [\n               \"2\"\n           ],\n           \"partner_priority\": false,\n           \"partner_country\": \"PH\",\n           \"partner_rate\": 5,\n           \"partner_currency\": \"PHP\",\n           \"created\": 1636462504\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/devices"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/sms.pending",
    "title": "Get Pending Messages",
    "name": "Get_Pending_Messages",
    "description": "<p>Get Pending Messages. Requires &quot;<strong>get_sms_pending</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/sms.pending?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/sms.pending\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Pending SMS Messages\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"mode\": \"Devices\",\n           \"sender\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender_type\": \"device\",\n           \"sim\": 2,\n           \"priority\": false,\n           \"api\": false,\n           \"recipient\": \"+639184661533\",\n           \"message\": \"Hello World!\",\n           \"created\": 1645520349\n       },\n       {\n           \"id\": 2,\n           \"mode\": \"Devices\",\n           \"sender\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender_type\": \"device\",\n           \"sim\": 2,\n           \"priority\": false,\n           \"api\": false,\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"created\": 1645520349\n       },\n       {\n           \"id\": 3,\n           \"mode\": \"Credits\",\n           \"sender\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender_type\": \"partner_device\",\n           \"sim\": 2,\n           \"priority\": false,\n           \"api\": false,\n           \"recipient\": \"+639184661532\",\n           \"message\": \"Hello World!\",\n           \"created\": 1645520349\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/sms.pending"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/sms.received",
    "title": "Get Received Messages",
    "name": "Get_Received_Messages",
    "description": "<p>Get Received Messages. Requires &quot;<strong>get_sms_received</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/sms.received?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/sms.received\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Received SMS Messages\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender\": \"+639760713666\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644405663\n       },\n       {\n           \"id\": 33,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender\": \"GCash\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644417283\n       },\n       {\n           \"id\": 22,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender\": \"TWILIO\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644421353\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/sms.received"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/sms.campaigns",
    "title": "Get SMS Campaigns",
    "name": "Get_SMS_Campaigns",
    "description": "<p>Get SMS Campaigns. Requires &quot;<strong>get_sms_campaigns</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/sms.campaigns?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/sms.campaigns\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"SMS Campaigns\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"sender\": \"f1939aec-0d08-e221-3582-400511111108\",\n           \"sender_type\": \"device\",\n           \"status\": \"completed\",\n           \"name\": \"Campaign Test\",\n           \"contacts\": 32,\n           \"created\": 1662763259\n       },\n       {\n           \"id\": 6,\n           \"sender\": \"f1939aec-0d08-e221-3582-400511111108\",\n           \"sender_type\": \"device\",\n           \"status\": \"running\",\n           \"name\": \"World Test\",\n           \"contacts\": 2,\n           \"created\": 1662799690\n       },\n       {\n           \"id\": 14,\n           \"sender\": \"da6fcb98-3c6c-4554-3582-400511111108\",\n           \"sender_type\": \"device\",\n           \"status\": \"paused\",\n           \"name\": \"Marketing\",\n           \"contacts\": 8,\n           \"created\": 1662828578\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/sms.campaigns"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/sms.sent",
    "title": "Get Sent Messages",
    "name": "Get_Sent_Messages",
    "description": "<p>Get Sent Messages. Requires &quot;<strong>get_sms_sent</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/sms.sent?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/sms.sent\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Sent SMS Messages\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"mode\": \"Devices\",\n           \"sender\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender_type\": \"device\",\n           \"sim\": 2,\n           \"priority\": false,\n           \"api\": false,\n           \"status\": \"sent\",\n           \"status_code\": \"SMS_SENT\",\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644382599\n       },\n       {\n           \"id\": 2,\n           \"mode\": \"Devices\",\n           \"sender\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sender_type\": \"device\",\n           \"sim\": 2,\n           \"priority\": false,\n           \"api\": false,\n           \"status\": \"sent\",\n           \"status_code\": \"SMS_SENT\",\n           \"recipient\": \"+639184661533\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644382597\n       },\n       {\n           \"id\": 3,\n           \"mode\": \"Credits\",\n           \"sender\": \"Twilio\",\n           \"sender_type\": \"gateway\",\n           \"sim\": 0,\n           \"priority\": false,\n           \"api\": false,\n           \"status\": \"sent\",\n           \"status_code\": \"\",\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"created\": 1644382807\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/sms.sent"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/sms.bulk",
    "title": "Send Bulk Messages",
    "name": "Send_Bulk_Message",
    "description": "<p>Send bulk sms messages. Requires &quot;<strong>sms_send_bulk</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"devices\"",
              "\"credits\""
            ],
            "optional": false,
            "field": "mode",
            "description": "<p>Mode of sending the message, it can be &quot;devices&quot; which will allow you to use your linked android devices or &quot;credits&quot; which will allow you to use gateways and partner devices. &quot;credits&quot; requires you to have enough credit balance to send messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "campaign",
            "description": "<p>Name of the campaign, you will see this in the sms campaign manager.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "numbers",
            "description": "<p>List of phone numbers separated by commas. It can be optional if &quot;groups&quot; parameter is not empty. It will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br> <strong>Example for Philippines</strong><br> E.164: +639184661533<br> Local: 09184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "groups",
            "description": "<p>List of contact group ID's separated by commas. It can be optional if &quot;numbers&quot; parameter is not empty. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Message you want to send, spintax and shortcodes are supported.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device",
            "description": "<p>Linked device unique ID, this is required if you will send with &quot;devices&quot; mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).</p>"
          },
          {
            "group": "Parameter",
            "type": "String|Number",
            "optional": true,
            "field": "gateway",
            "description": "<p>Partner device unique ID or gateway ID, this is required if you will send with &quot;credits&quot; mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": false,
            "field": "sim",
            "description": "<p>Sim slot number you want to use. For &quot;devices&quot; mode only.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": true,
            "field": "priority",
            "defaultValue": "1",
            "description": "<p>If you want to send the messages as priority, 1 for yes and 2 for no. For &quot;devices&quot; mode only.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "shortener",
            "defaultValue": "none",
            "description": "<p>Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $message = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"mode\" => \"devices\",\n      \"campaign\" => \"bulk test\",\n      \"numbers\" => \"+639123456789,+639123456789,+639123456789\",\n      \"groups\" => \"1,2,3,4\",\n      \"device\" => \"00000000-0000-0000-d57d-f30cb6a89289\",\n      \"sim\" => 1,\n      \"priority\" => 1,\n      \"message\" => \"Hello World!\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/sms.bulk\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nmessage = {\n    \"secret\": apiSecret,\n    \"mode\": \"devices\",\n    \"campaign\": \"bulk test\",\n    \"numbers\": \"+639123456789,+639123456789,+639123456789\",\n    \"groups\": \"1,2,3,4\",\n    \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n    \"sim\": 1,\n    \"priority\": 1,\n    \"message\": \"Hello World!\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/sms.bulk\", params = message)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Message has been queued for sending!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/sms.bulk"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/sms",
    "title": "Send Single Message",
    "name": "Send_Single_Message",
    "description": "<p>Send a single sms message. Requires &quot;<strong>sms_send</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"devices\"",
              "\"credits\""
            ],
            "optional": false,
            "field": "mode",
            "description": "<p>Mode of sending the message, it can be &quot;devices&quot; which will allow you to use your linked android devices or &quot;credits&quot; which will allow you to use gateways and partner devices. &quot;credits&quot; requires you to have enough credit balance to send messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>Recipient mobile number, it will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br> <strong>Example for Philippines</strong><br> E.164: +639184661533<br> Local: 09184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Message you want to send, spintax is also supported.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device",
            "description": "<p>Linked device unique ID, this is required if you will send with &quot;devices&quot; mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).</p>"
          },
          {
            "group": "Parameter",
            "type": "String|Number",
            "optional": true,
            "field": "gateway",
            "description": "<p>Partner device unique ID or gateway ID, this is required if you will send with &quot;credits&quot; mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": false,
            "field": "sim",
            "description": "<p>Sim slot number you want to use. For &quot;devices&quot; mode only.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": true,
            "field": "priority",
            "defaultValue": "1",
            "description": "<p>If you want to send the messages as priority, 1 for yes and 2 for no. For &quot;devices&quot; mode only.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "shortener",
            "defaultValue": "none",
            "description": "<p>Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $message = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"mode\" => \"devices\",\n      \"device\" => \"00000000-0000-0000-d57d-f30cb6a89289\",\n      \"sim\" => 1,\n      \"priority\" => 1,\n      \"phone\" => \"+639123456789\",\n      \"message\" => \"Hello World!\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/sms\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nmessage = {\n    \"secret\": apiSecret,\n    \"mode\": \"devices\",\n    \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n    \"sim\": 1,\n    \"priority\": 1,\n    \"phone\": \"+639123456789\",\n    \"message\": \"Hello World!\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/sms\", params = message)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Message has been queued for sending!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/sms"
      }
    ]
  },
  {
    "type": "get",
    "url": "/remote/start.sms",
    "title": "Start SMS Campaign",
    "name": "Start_SMS_Campaign",
    "description": "<p>Start SMS Campaign. Requires &quot;<strong>start_sms_campaign</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "campaign",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/remote/start.sms?secret={$apiSecret}&campaign={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/remote/start.sms\", params = {\n    \"secret\": apiSecret,\n    \"campaign\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"SMS campaign has been resumed!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/remote/start.sms"
      }
    ]
  },
  {
    "type": "get",
    "url": "/remote/stop.sms",
    "title": "Stop SMS Campaign",
    "name": "Stop_SMS_Campaign",
    "description": "<p>Stop SMS Campaign. Requires &quot;<strong>stop_sms_campaign</strong>&quot; API permission.</p>",
    "group": "SMS",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "campaign",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/remote/stop.sms?secret={$apiSecret}&campaign={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/remote/stop.sms\", params = {\n    \"secret\": apiSecret,\n    \"campaign\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"SMS campaign has been paused!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "SMS",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/remote/stop.sms"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/rates",
    "title": "Get Gateway Rates",
    "name": "Get_Gateway_Rates",
    "description": "<p>Get Gateway Rates. Requires &quot;<strong>get_rates</strong>&quot; API permission.</p>",
    "group": "System",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/rates?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/rates\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Gateway Rates\",\n  \"data\": {\n       \"gateways\": [\n           {\n               \"id\": 1,\n               \"name\": \"Twilio\",\n               \"currency\": \"GBP\",\n               \"pricing\": {\n                   \"default\": \"0.01\",\n                   \"countries\": {\n                       \"us\": \"0.01\",\n                       \"ph\": \"10\",\n                       \"gb\": \"0.02\"\n                   }\n               }\n           }\n       ],\n       \"partners\": [\n           {\n               \"unique\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n               \"name\": \"F11 Phone\",\n               \"version\": \"Android 11\",\n               \"priority\": false,\n               \"sim\": [\n                   \"2\"\n               ],\n               \"country\": \"PH\",\n               \"currency\": \"PHP\",\n               \"rate\": 5,\n               \"owner\": \"mail@owneremail.com\",\n               \"status\": \"online\"\n           }\n       ]\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "System",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/rates"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/shorteners",
    "title": "Get Shorteners",
    "name": "Get_Shorteners",
    "description": "<p>Get Shorteners. Requires &quot;<strong>get_shorteners</strong>&quot; API permission.</p>",
    "group": "System",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/shorteners?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/shorteners\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Available Shorteners\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"name\": \"Bitly\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "System",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/shorteners"
      }
    ]
  },
  {
    "type": "get",
    "url": "/clear/ussd",
    "title": "Clear Pending USSD",
    "name": "Clear_Pending_USSD",
    "description": "<p>Clear Pending USSD. Requires &quot;<strong>clear_ussd_pending</strong>&quot; API permission.</p>",
    "group": "USSD",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example ",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/clear/ussd?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/clear/ussd\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Pending USSD requests has been cleared!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "USSD",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/clear/ussd"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/ussd",
    "title": "Delete USSD Request",
    "name": "Delete_USSD_Request",
    "description": "<p>Delete USSD Request. Requires &quot;<strong>delete_ussd</strong>&quot; API permission.</p>",
    "group": "USSD",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>USSD request ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $ussdId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/ussd?secret={$apiSecret}&id={$ussdId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nussdId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/ussd\", params = {\n    \"secret\": apiSecret\n    \"id\": ussdId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"USSD request has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "USSD",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/ussd"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/ussd",
    "title": "Get USSD Requests",
    "name": "Get_USSD_Requests",
    "description": "<p>Get USSD Requests. Requires &quot;<strong>get_ussd</strong>&quot; API permission.</p>",
    "group": "USSD",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/ussd?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/ussd\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"USSD Requests\",\n  \"data\": [\n       {\n           \"id\": 5,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sim\": 1,\n           \"code\": \"*143#\",\n           \"response\": \"Sorry! You are not allowed to use this service.\",\n           \"status\": \"completed\",\n           \"created\": 1645043019\n       },\n       {\n           \"id\": 6,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sim\": 1,\n           \"code\": \"*145#\",\n           \"response\": \"Your balance is 14.60\",\n           \"status\": \"completed\",\n           \"created\": 1645043024\n       },\n       {\n           \"id\": 13,\n           \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\",\n           \"sim\": 2,\n           \"code\": \"*121#\",\n           \"response\": \"Sorry! Invalid MMI Code.\",\n           \"status\": \"completed\",\n           \"created\": 1645413608\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "USSD",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/ussd"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/ussd",
    "title": "Send USSD Request",
    "name": "Send_USSD_Request",
    "description": "<p>Send USSD Request. Requires &quot;<strong>ussd</strong>&quot; API permission.</p>",
    "group": "USSD",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>MMI request code. Please make sure that you are using a valid MMI code, if not, it will fail.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "sim",
            "description": "<p>Sim slot number you want to use.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device",
            "description": "<p>Linked device unique ID. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $ussd = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"code\" => \"*121#\",\n      \"sim\" => 1,\n      \"device\" => \"00000000-0000-0000-d57d-f30cb6a89289\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/ussd\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $ussd);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nussd = {\n    \"secret\": apiSecret,\n    \"code\": \"*121#\",\n    \"sim\": 1,\n    \"device\": \"00000000-0000-0000-d57d-f30cb6a89289\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/ussd\", params = ussd)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp message has been queued for sending!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "USSD",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/ussd"
      }
    ]
  },
  {
    "type": "get",
    "url": "/create/whatsapp",
    "title": "Create WhatsApp QRCode",
    "name": "Create_WhatsApp_QRCode",
    "description": "<p>Create WhatsApp QRCode. Requires &quot;<strong>create_whatsapp</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $contact = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/create/whatsapp\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $contact);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\ncontact = {\n    \"secret\": apiSecret\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/create/whatsapp\", params = contact)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp QRCode has been created!\",\n  \"data\": {\n       qrstring: \"2@MwggDzdZqWfC4vYBJQsExNnSuE6+fyGYVo+/RfMyWUxJBW2Q0yDKykpqRi+pSoHquonRk5P6CaVOsg==,BpVhDS5yHBbN9k/xCiQIWwOduYcyo/1tMhoWaNpwJC8=,7F75UfkJzXY6GbLy+3evLc9aCkyN40K2ORR0dZ84eSk=,7nQ0NTR4eaXRZOwIbv9FKoFpFTSNu6fHzKGaICsyDzc=\",\n       qrimagelink: \"http://127.0.0.1/zender/api/get/wq.qr?token=e10adc3949ba59abbe56e057f20f883e&qrstring=2@MwggDzdZqWfC4vYBJQsExNnSuE6+fyGYVo+/RfMyWUxJBW2Q0yDKykpqRi+pSoHquonRk5P6CaVOsg==,BpVhDS5yHBbN9k/xCiQIWwOduYcyo/1tMhoWaNpwJC8=,7F75UfkJzXY6GbLy+3evLc9aCkyN40K2ORR0dZ84eSk=,7nQ0NTR4eaXRZOwIbv9FKoFpFTSNu6fHzKGaICsyDzc=\",\n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/whatsapp"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/wa.received",
    "title": "Delete Received Chat",
    "name": "Delete_Received_Chat",
    "description": "<p>Delete Received Chat. Requires &quot;<strong>delete_wa_received</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Received chat ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $chatId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/wa.received?secret={$apiSecret}&id={$chatId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nchatId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/wa.received\", params = {\n    \"secret\": apiSecret\n    \"id\": chatId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Received WhatsApp chat has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/wa.received"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/wa.sent",
    "title": "Delete Sent Chat",
    "name": "Delete_Sent_Chat",
    "description": "<p>Delete Sent Chat. Requires &quot;<strong>delete_wa_sent</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Sent chat ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $chatId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/wa.sent?secret={$apiSecret}&id={$chatId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\nchatId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/wa.sent\", params = {\n    \"secret\": apiSecret\n    \"id\": chatId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Sent WhatsApp chat has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/wa.sent"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/wa.campaign",
    "title": "Delete WhatsApp Campaign",
    "name": "Delete_WhatsApp_Campaign",
    "description": "<p>Delete WhatsApp Campaign. Requires &quot;<strong>delete_wa_campaign</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/delete/wa.campaign?secret={$apiSecret}&id={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/delete/wa.campaign\", params = {\n    \"secret\": apiSecret\n    \"id\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp campaign has been deleted!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/wa.campaign"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.accounts",
    "title": "Get Accounts",
    "name": "Get_Accounts",
    "description": "<p>Get Accounts. Requires &quot;<strong>get_wa_accounts</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/wa.accounts?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/wa.accounts\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp Accounts\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"phone\": \"+639760713666\",\n           \"unique\": \"90cf7d40a467d5f40a39fca222c216449cb9abee73e5e2b0b321060c2ae06a8fa9e45486\",\n           \"created\": 1645128758\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.accounts"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.pending",
    "title": "Get Pending Chats",
    "name": "Get_Pending_Chats",
    "description": "<p>Get Pending Chats. Requires &quot;<strong>get_wa_pending</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/wa.pending?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/wa.pending\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Pending WhatsApp Chats\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"account\": \"+639760713666\",\n           \"api\": false,\n           \"recipient\": \"+639184661533\",\n           \"message\": \"Hello World!\",\n           \"attachment\": false,\n           \"created\": 1645521446\n       },\n       {\n           \"id\": 2,\n           \"account\": \"+639760713666\",\n           \"api\": true,\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"attachment\": false,\n           \"created\": 1645521446\n       },\n       {\n           \"id\": 3,\n           \"account\": \"+639760713666\",\n           \"api\": false,\n           \"recipient\": \"+639184661532\",\n           \"message\": \"Hello World!\",\n           \"attachment\": \"http://127.0.0.1/zender/uploads/whatsapp/c4ca4238a0b923820dcc509a6f75849b_c4ca4238a0b923820dcc509a6f75849b6352420c0654f1.46673324.pdf\",\n           \"created\": 1645521446\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.pending"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.received",
    "title": "Get Received Chats",
    "name": "Get_Received_Chats",
    "description": "<p>Get Received Chats. Requires &quot;<strong>get_wa_received</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/wa.received?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/wa.received\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Pending WhatsApp Chats\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"account\": \"+639760713666\",\n           \"recipient\": \"+639184661533\",\n           \"message\": \"Hello world!\",\n           \"created\": 1645232578\n       },\n       {\n           \"id\": 2,\n           \"account\": \"+639760713666\",\n           \"recipient\": \"+639184661533\",\n           \"message\": \"How are you?\",\n           \"created\": 1645232635\n       },\n       {\n           \"id\": 3,\n           \"account\": \"+639760713666\",\n           \"recipient\": \"+639184661533\",\n           \"message\": \"hahaha\",\n           \"created\": 1645232650\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.received"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.sent",
    "title": "Get Sent Chats",
    "name": "Get_Sent_Chats",
    "description": "<p>Get Sent Chats. Requires &quot;<strong>get_wa_sent</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/wa.sent?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/wa.sent\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"Sent WhatsApp Chats\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"account\": \"+639760713666\",\n           \"status\": \"sent\",\n           \"api\": true,\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"attachment\": false,\n           \"created\": 1645129261\n       },\n       {\n           \"id\": 2,\n           \"account\": \"+639760713666\",\n           \"status\": \"sent\",\n           \"api\": false,\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"attachment\": false,\n           \"created\": 1645129261\n       },\n       {\n           \"id\": 3,\n           \"account\": \"+639760713666\",\n           \"status\": \"failed\",\n           \"api\": true,\n           \"recipient\": \"+639206150513\",\n           \"message\": \"Hello World!\",\n           \"attachment\": \"http://127.0.0.1/zender/uploads/whatsapp/c4ca4238a0b923820dcc509a6f75849b_c4ca4238a0b923820dcc509a6f75849b6352420c0654f1.46673324.pdf\",\n           \"created\": 1645129720\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.sent"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.campaigns",
    "title": "Get WhatsApp Campaigns",
    "name": "Get_WhatsApp_Campaigns",
    "description": "<p>Get WhatsApp Campaigns. Requires &quot;<strong>get_wa_campaigns</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>Limit the number of results per page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>Pagination of results.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/get/wa.campaigns?secret={$apiSecret}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/get/wa.campaigns\", params = {\n    \"secret\": apiSecret\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp Campaigns\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"account\": \"+639123456789\",\n           \"type\": \"text\",\n           \"status\": \"completed\",\n           \"name\": \"Sample Campaign\",\n           \"contacts\": 2,\n           \"created\": 1661446601\n       },\n       {\n           \"id\": 2,\n           \"account\": \"+639123456789\",\n           \"type\": \"button\",\n           \"status\": \"running\",\n           \"name\": \"Sample Campaign 2\",\n           \"contacts\": 70,\n           \"created\": 1661447664\n       },\n       {\n           \"id\": 3,\n           \"account\": \"+639123456789\",\n           \"type\": \"media\",\n           \"status\": \"paused\",\n           \"name\": \"Demo\",\n           \"contacts\": 5,\n           \"created\": 1663185427\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.campaigns"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/wa.qr",
    "title": "Get WhatsApp QR Image",
    "name": "Get_WhatsApp_QR_Image",
    "description": "<p>Get WhatsApp QR Image. Requires &quot;<strong>create_whatsapp</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>The token string you got from</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "qrstring",
            "description": "<p>The qr code string you got from <strong>/create/whatsapp</strong></p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/wa.qr"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/whatsapp.bulk",
    "title": "Send Bulk Chats",
    "name": "Send_Bulk_Chats",
    "description": "<p>Send bulk chat messages. Requires &quot;<strong>wa_send_bulk</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "account",
            "description": "<p>WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "campaign",
            "description": "<p>Name of the campaign, you will see this in the WhatsApp campaign manager.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "recipients",
            "description": "<p>List of phone numbers or group addresses separated by commas. It can be optional if &quot;groups&quot; parameter is not empty. It will accept whatsapp group address E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br> <strong>Example for Philippines</strong><br> E.164: +639184661533<br> Local: 09184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "groups",
            "description": "<p>List of contact group ID's separated by commas. It can be optional if &quot;numbers&quot; parameter is not empty. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"text\"",
              "\"media\"",
              "\"button\"",
              "\"list\"",
              "\"template\""
            ],
            "optional": false,
            "field": "type",
            "description": "<p>Type of WhatsApp message.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Message or caption you want to send, spintax and shortcodes are supported.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "media_file",
            "description": "<p>This is for &quot;media&quot; and &quot;button&quot; type message only. The media file you want to attach in the WhatsApp message, it supports jpg, png, gif and mp4 files. Please use POST method if you are using this parameter. &quot;mp4&quot; files are not supported in &quot;button&quot; type messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "media_url",
            "description": "<p>This is for &quot;media&quot; and &quot;button&quot; type message only. The media file url, please use direct link to the media file. It will be downloaded and be attached in the WhatsApp message, it also supports jpg, png, gif and mp4 files. You can use GET method for this, &quot;mp4&quot; files are not supported on button messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"image\"",
              "\"mp4\""
            ],
            "optional": true,
            "field": "media_type",
            "description": "<p>This is for &quot;media&quot; type message only. You only need to enter this parameter if you are using &quot;media_url&quot; instead of &quot;media_file&quot;. You need to declare the file type of the media in the url you provided.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "document_file",
            "description": "<p>This is for &quot;document&quot; type message only. The document file you want to attach in the WhatsApp message, it supports pdf, xls, xlsx, doc and docx files. Please use POST method if you are using this parameter.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "document_url",
            "description": "<p>This is for &quot;document&quot; type message only. The document file url, please use direct link to the document file. It will be downloaded and be attached in the WhatsApp message, it also supports pdf, xls, xlsx, doc, and docx files. You can use GET method for this.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"pdf\"",
              "\"xls\"",
              "\"xlsx\"",
              "\"doc\"",
              "\"docx\""
            ],
            "optional": true,
            "field": "document_type",
            "description": "<p>This is for &quot;document&quot; type message only. You only need to enter this parameter if you are using &quot;document_url&quot; instead of &quot;document_file&quot;. You need to declare the file type of the document in the url you provided.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "button_1",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the first button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "button_2",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the second button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "button_3",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the third button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "list_title",
            "description": "<p>This is for &quot;list&quot; type message only. The title of the list. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "menu_title",
            "description": "<p>This is for &quot;list&quot; type message only. The title of the menu. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "footer",
            "description": "<p>This is for &quot;list&quot; and &quot;template&quot; type message only. The footer message. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "format",
            "description": "<p>This is for &quot;list&quot; and &quot;template&quot; type message only. The list or template format you want to send. This must be a valid JSON string, please check the example JSON in the quick message modal. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "shortener",
            "defaultValue": "none",
            "description": "<p>Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $chat = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"account\" => 1,\n      \"campaign\" => \"bulk test\",\n      \"numbers\" => \"+639123456789,+639123456789,+639123456789\",\n      \"groups\" => \"1,2,3,4\",\n      \"phone\" => \"+639123456789\",\n      \"message\" => \"Hello World!\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/whatsapp.bulk\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nchat = {\n    \"secret\": apiSecret,\n    \"account\": 1,\n    \"campaign\": \"bulk test\",\n    \"numbers\": \"+639123456789,+639123456789,+639123456789\",\n    \"groups\": \"1,2,3,4\",\n    \"phone\": \"+639123456789\",\n    \"message\": \"Hello World!\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/whatsapp.bulk\", params = chat)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp bulk chats has been queued!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = WhatsApp account doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/whatsapp.bulk"
      }
    ]
  },
  {
    "type": "post",
    "url": "/send/whatsapp",
    "title": "Send Single Chat",
    "name": "Send_Single_Chat",
    "description": "<p>Send a single chat message. Requires &quot;<strong>wa_send</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "account",
            "description": "<p>WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "recipient",
            "description": "<p>Recipient mobile number or group address, it will accept whatsapp group address or E.164 formatted number and locally formatted numbers using the country code from your profile settings.<br> <strong>Example for Philippines</strong><br> E.164: +639184661533<br> Local: 09184661533</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"text\"",
              "\"media\"",
              "\"document\"",
              "\"button\"",
              "\"list\"",
              "\"template\""
            ],
            "optional": false,
            "field": "type",
            "description": "<p>Type of WhatsApp message.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Message or caption you want to send, spintax is also supported.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "media_file",
            "description": "<p>This is for &quot;media&quot; and &quot;button&quot; type message only. The media file you want to attach in the WhatsApp message, it supports jpg, png, gif and mp4 files. Please use POST method if you are using this parameter. &quot;mp4&quot; files are not supported in &quot;button&quot; type messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "media_url",
            "description": "<p>This is for &quot;media&quot; and &quot;button&quot; type message only. The media file url, please use direct link to the media file. It will be downloaded and be attached in the WhatsApp message, it also supports jpg, png, gif and mp4 files. You can use GET method for this, &quot;mp4&quot; files are not supported on button messages.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"image\"",
              "\"mp4\""
            ],
            "optional": true,
            "field": "media_type",
            "description": "<p>This is for &quot;media&quot; type message only. You only need to enter this parameter if you are using &quot;media_url&quot; instead of &quot;media_file&quot;. You need to declare the file type of the media in the url you provided.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "document_file",
            "description": "<p>This is for &quot;document&quot; type message only. The document file you want to attach in the WhatsApp message, it supports pdf, xls, xlsx, doc and docx files. Please use POST method if you are using this parameter.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "document_url",
            "description": "<p>This is for &quot;document&quot; type message only. The document file url, please use direct link to the document file. It will be downloaded and be attached in the WhatsApp message, it also supports pdf, xls, xlsx, doc, and docx files. You can use GET method for this.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"pdf\"",
              "\"xls\"",
              "\"xlsx\"",
              "\"doc\"",
              "\"docx\""
            ],
            "optional": true,
            "field": "document_type",
            "description": "<p>This is for &quot;document&quot; type message only. You only need to enter this parameter if you are using &quot;document_url&quot; instead of &quot;document_file&quot;. You need to declare the file type of the document in the url you provided.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "button_1",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the first button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "button_2",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the second button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "button_3",
            "description": "<p>This is for &quot;button&quot; type message only. The name for the third button.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "list_title",
            "description": "<p>This is for &quot;list&quot; type message only. The title of the list. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "menu_title",
            "description": "<p>This is for &quot;list&quot; type message only. The title of the menu. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "footer",
            "description": "<p>This is for &quot;list&quot; and &quot;template&quot; type message only. The footer message. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "format",
            "description": "<p>This is for &quot;list&quot; and &quot;template&quot; type message only. The list or template format you want to send. This must be a valid JSON string, please check the example JSON in the quick message modal. Please use POST method for &quot;list&quot; and &quot;template&quot; formats.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "shortener",
            "defaultValue": "none",
            "description": "<p>Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $chat = [\n      \"secret\" => \"API_SECRET\", // your API secret from (Tools -> API Keys) page\n      \"account\" => 1,\n      \"recipient\" => \"+639123456789\",\n      \"type\" => \"text\",\n      \"message\" => \"Hello World!\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/api/send/whatsapp\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\n\nchat = {\n    \"secret\": apiSecret,\n    \"account\": 1,\n    \"recipient\": \"+639123456789\",\n    \"type\": \"text\",\n    \"message\": \"Hello World!\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/api/send/whatsapp\", params = chat)\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp message has been queued for sending!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = WhatsApp account doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/send/whatsapp"
      }
    ]
  },
  {
    "type": "get",
    "url": "/remote/start.chats",
    "title": "Start WhatsApp Campaign",
    "name": "Start_WhatsApp_Campaign",
    "description": "<p>Start WhatsApp Campaign. Requires &quot;<strong>start_wa_campaign</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "campaign",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/remote/start.chats?secret={$apiSecret}&campaign={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/remote/start.chats\", params = {\n    \"secret\": apiSecret,\n    \"campaign\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp campaign has been resumed!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/remote/start.chats"
      }
    ]
  },
  {
    "type": "get",
    "url": "/remote/stop.chats",
    "title": "Stop WhatsApp Campaign",
    "name": "Stop_WhatsApp_Campaign",
    "description": "<p>Stop WhatsApp Campaign. Requires &quot;<strong>stop_wa_campaign</strong>&quot; API permission.</p>",
    "group": "WhatsApp",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "secret",
            "description": "<p>The API secret you copied from (Tools -&gt; API Keys) page</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "campaign",
            "description": "<p>Campaign ID</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $apiSecret = \"API_SECRET\"; // your API secret from (Tools -> API Keys) page\n  $campaignId = 1;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/api/remote/stop.chats?secret={$apiSecret}&campaign={$campaignId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# your API secret from (Tools -> API Keys) page\napiSecret = \"API_SECRET\"\ncampaignId = 1\n\nr = requests.get(url = \"http://127.0.0.1/zender/api/remote/stop.chats\", params = {\n    \"secret\": apiSecret,\n    \"campaign\": campaignId\n})\n  \n# do something with response object\nresult = r.json()",
        "type": "python"
      }
    ],
    "success": {
      "fields": {
        "Success Response Format": [
          {
            "group": "Success Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes <br> 200 = Success</p>"
          },
          {
            "group": "Success Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Success Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Response",
          "content": "{\n  \"status\": 200,\n  \"message\": \"WhatsApp campaign has been resumed!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error Response Format": [
          {
            "group": "Error Response Format",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>List of Codes<br> 400 = Invalid parameters<br> 401 = Invalid API secret<br> 403 = Access denied<br> 404 = Device doesn't exist<br> 500 = Something went wrong</p>"
          },
          {
            "group": "Error Response Format",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>Response message</p>"
          },
          {
            "group": "Error Response Format",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>Array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error Response",
          "content": "{\n  \"status\": 400,\n  \"message\": \"Invalid Parameters!\",\n  \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "WhatsApp",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/remote/stop.chats"
      }
    ]
  }
] });