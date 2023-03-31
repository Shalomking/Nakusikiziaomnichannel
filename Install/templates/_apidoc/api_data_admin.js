define({ "api": [
  {
    "type": "get",
    "url": "/get/languages",
    "title": "Get Languages",
    "name": "Get_Languages",
    "description": "<p>Get the list of languages.</p>",
    "group": "Languages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/languages?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/languages\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Languages\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"order\": 2,\n           \"rtl\": false,\n           \"iso\": \"US\",\n           \"name\": \"English\"\n       },\n       {\n           \"id\": 2,\n           \"order\": 1,\n           \"rtl\": false,\n           \"iso\": \"PH\",\n           \"name\": \"Filipino\"\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Languages",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/languages"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/package",
    "title": "Create Package",
    "name": "Create_Package",
    "description": "<p>Create a new package.</p>",
    "group": "Packages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of the package.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "price",
            "description": "<p>Price of the package, cannot be less than 1. This is based on system currency.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": false,
            "field": "hidden",
            "description": "<p>Hide the package from homepage and modal. 1 for yes and 2 for no.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": false,
            "field": "footermark",
            "description": "<p>Include the footer mark in sms/chats. 1 for yes and 2 for no.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "send_limit",
            "description": "<p>SMS send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "receive_limit",
            "description": "<p>SMS receive limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "ussd_limit",
            "description": "<p>USSD limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "notification_limit",
            "description": "<p>Notification limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "contact_limit",
            "description": "<p>Maximum number of contacts that can be saved. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "device_limit",
            "description": "<p>Maximum number of devices that can be linked. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "key_limit",
            "description": "<p>Maximum number of API keys that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "webhook_limit",
            "description": "<p>Maximum number of webhooks that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "action_limit",
            "description": "<p>Maximum number of actions that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "scheduled_limit",
            "description": "<p>Maximum number of scheduled sms/chats that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "wa_send_limit",
            "description": "<p>WhatsApp send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "wa_receive_limit",
            "description": "<p>WhatsAPp send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "wa_account_limit",
            "description": "<p>Maximum number of WhatsApp accounts that can be linked. Use <strong>0</strong> for unlimited.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $package = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"name\" => \"Unlimited\",\n      \"price\" => 30,\n      \"hidden\" => 2,\n      \"footermark\" => 1,\n      \"send_limit\" => 0,\n      \"receive_limit\" => 0,\n      \"ussd_limit\" => 100,\n      \"notification_limit\" => 0,\n      \"contact_limit\" => 100,\n      \"device_limit\" => 5,\n      \"key_limit\" => 5,\n      \"webhook_limit\" => 10,\n      \"action_limit\" => 5,\n      \"scheduled_limit\" => 100,\n      \"wa_send_limit\" => 0,\n      \"wa_receive_limit\" => 0,\n      \"wa_account_limit\" => 5\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/create/package\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $package);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\npackage = {\n    \"token\": systemToken,\n    \"name\": \"Unlimited\",\n    \"price\": 30,\n    \"hidden\": 2,\n    \"footermark\": 1,\n    \"send_limit\": 0,\n    \"receive_limit\": 0,\n    \"ussd_limit\": 100,\n    \"notification_limit\": 0,\n    \"contact_limit\": 100,\n    \"device_limit\": 5,\n    \"key_limit\": 5,\n    \"webhook_limit\": 10,\n    \"action_limit\": 5,\n    \"scheduled_limit\": 100,\n    \"wa_send_limit\": 0,\n    \"wa_receive_limit\": 0,\n    \"wa_account_limit\": 5\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/create/package\", params = package)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Package has been created!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Packages",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/package"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/package",
    "title": "Delete Package",
    "name": "Delete_Package",
    "description": "<p>Delete an existing package.</p>",
    "group": "Packages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Package ID, you can obtain a package ID from <strong>/get/packages</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $packageId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/package?token={$systemToken}&id={$packageId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\npackageId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/package\", params = {\n    \"token\": systemToken,\n    \"id\": packageId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Role has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Packages",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/package"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/packages",
    "title": "Get Packages",
    "name": "Get_Packages",
    "description": "<p>Get the list of packages.</p>",
    "group": "Packages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/packages?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/packages\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Packages\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"price\": 0,\n           \"hidden\": false,\n           \"footermark\": true,\n           \"name\": \"Starter\",\n           \"sms_send_limit\": 1000,\n           \"sms_receive_limit\": 250,\n           \"ussd_limit\": 0,\n           \"notification_limit\": 0,\n           \"device_limit\": 3,\n           \"wa_send_limit\": 0,\n           \"wa_receive_limit\": 0,\n           \"wa_account_limit\": 0,\n           \"scheduled_limit\": 0,\n           \"key_limit\": 5,\n           \"webhook_limit\": 5,\n           \"action_limit\": 0,\n           \"created\": 1586370407\n       },\n       {\n           \"id\": 2,\n           \"price\": 12,\n           \"hidden\": true,\n           \"footermark\": false,\n           \"name\": \"Professional\",\n           \"sms_send_limit\": 3000,\n           \"sms_receive_limit\": 1500,\n           \"ussd_limit\": 0,\n           \"notification_limit\": 0,\n           \"device_limit\": 30,\n           \"wa_send_limit\": 0,\n           \"wa_receive_limit\": 0,\n           \"wa_account_limit\": 0,\n           \"scheduled_limit\": 0,\n           \"key_limit\": 10,\n           \"webhook_limit\": 5,\n           \"action_limit\": 0,\n           \"created\": 1587393358\n       },\n       {\n           \"id\": 3,\n           \"price\": 30,\n           \"hidden\": false,\n           \"footermark\": false,\n           \"name\": \"Enterprise\",\n           \"sms_send_limit\": 10000,\n           \"sms_receive_limit\": 7000,\n           \"ussd_limit\": 0,\n           \"notification_limit\": 0,\n           \"device_limit\": 50,\n           \"wa_send_limit\": 0,\n           \"wa_receive_limit\": 0,\n           \"wa_account_limit\": 0,\n           \"scheduled_limit\": 0,\n           \"key_limit\": 25,\n           \"webhook_limit\": 15,\n           \"action_limit\": 0,\n           \"created\": 1587393393\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Packages",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/packages"
      }
    ]
  },
  {
    "type": "post",
    "url": "/update/package",
    "title": "Update Package",
    "name": "Update_Package",
    "description": "<p>Update an existing package.</p>",
    "group": "Packages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Package ID, you can obtain package ID from <strong>/get/packages</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "name",
            "description": "<p>Name of the package.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "price",
            "description": "<p>Price of the package, cannot be less than 1. This is based on system currency.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": true,
            "field": "hidden",
            "description": "<p>Hide the package from homepage and modal. 1 for yes and 2 for no.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "allowedValues": [
              "1",
              "2"
            ],
            "optional": true,
            "field": "footermark",
            "description": "<p>Include the footer mark in sms/chats. 1 for yes and 2 for no.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "send_limit",
            "description": "<p>SMS send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "receive_limit",
            "description": "<p>SMS receive limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "ussd_limit",
            "description": "<p>USSD limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "notification_limit",
            "description": "<p>Notification limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "contact_limit",
            "description": "<p>Maximum number of contacts that can be saved. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "device_limit",
            "description": "<p>Maximum number of devices that can be linked. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "key_limit",
            "description": "<p>Maximum number of API keys that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "webhook_limit",
            "description": "<p>Maximum number of webhooks that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "action_limit",
            "description": "<p>Maximum number of actions that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "scheduled_limit",
            "description": "<p>Maximum number of scheduled sms/chats that can be created. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "wa_send_limit",
            "description": "<p>WhatsApp send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "wa_receive_limit",
            "description": "<p>WhatsAPp send limit every cycle. Use <strong>0</strong> for unlimited.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "wa_account_limit",
            "description": "<p>Maximum number of WhatsApp accounts that can be linked. Use <strong>0</strong> for unlimited.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $package = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"id\" => 7,\n      \"name\" => \"Unlimited\",\n      \"price\" => 30,\n      \"hidden\" => 2,\n      \"footermark\" => 1,\n      \"send_limit\" => 0,\n      \"receive_limit\" => 0,\n      \"ussd_limit\" => 100,\n      \"notification_limit\" => 0,\n      \"contact_limit\" => 100,\n      \"device_limit\" => 5,\n      \"key_limit\" => 5,\n      \"webhook_limit\" => 10,\n      \"action_limit\" => 5,\n      \"scheduled_limit\" => 100,\n      \"wa_send_limit\" => 0,\n      \"wa_receive_limit\" => 0,\n      \"wa_account_limit\" => 5\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/update/package\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $package);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\npackage = {\n    \"token\": systemToken,\n    \"id\": 5,\n    \"name\": \"Unlimited\",\n    \"price\": 30,\n    \"hidden\": 2,\n    \"footermark\": 1,\n    \"send_limit\": 0,\n    \"receive_limit\": 0,\n    \"ussd_limit\": 100,\n    \"notification_limit\": 0,\n    \"contact_limit\": 100,\n    \"device_limit\": 5,\n    \"key_limit\": 5,\n    \"webhook_limit\": 10,\n    \"action_limit\": 5,\n    \"scheduled_limit\": 100,\n    \"wa_send_limit\": 0,\n    \"wa_receive_limit\": 0,\n    \"wa_account_limit\": 5\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/update/package\", params = package)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Package has been updated!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Packages",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/update/package"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/role",
    "title": "Create Role",
    "name": "Create_Role",
    "description": "<p>Create a new user role.</p>",
    "group": "Roles",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of the user role.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"disallow_sms\"",
              "\"disallow_ussd\"",
              "\"disallow_notifications\"",
              "\"disallow_devices\"",
              "\"disallow_wa_chats\"",
              "\"disallow_wa_accounts\"",
              "\"disallow_contacts\"",
              "\"disallow_groups\"",
              "\"disallow_keys\"",
              "\"disallow_webhooks\"",
              "\"disallow_actions\"",
              "\"disallow_templates\"",
              "\"disallow_extensions\"",
              "\"disallow_redeem\"",
              "\"disallow_subscribe\"",
              "\"disallow_topup\"",
              "\"disallow_withdraw\"",
              "\"disallow_convert\"",
              "\"disallow_api\"",
              "\"manage_users\"",
              "\"manage_roles\"",
              "\"manage_packages\"",
              "\"manage_vouchers\"",
              "\"manage_subscriptions\"",
              "\"manage_transactions\"",
              "\"manage_widgets\"",
              "\"manage_pages\"",
              "\"manage_marketing\"",
              "\"manage_languages\"",
              "\"manage_gateways\"",
              "\"manage_shorteners\"",
              "\"manage_plugins\"",
              "\"manage_templates\"",
              "\"manage_api\""
            ],
            "optional": false,
            "field": "permissions",
            "description": "<p>List of permissions separrated by commas.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $role = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"name\" => \"New Role\",\n      \"permissions\" => \"disallow_api,manage_users,manage_roles\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/create/role\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $role);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nrole = {\n    \"token\": systemToken,\n    \"name\": \"New Role\",\n    \"permissions\": \"disallow_api,manage_users,manage_roles\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/create/role\", params = role)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Role has been created!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Roles",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/role"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/role",
    "title": "Delete Role",
    "name": "Delete_Role",
    "description": "<p>Delete a user role.</p>",
    "group": "Roles",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Role ID, you can obtain a role ID from <strong>/get/roles</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $roleId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/role?token={$systemToken}&id={$roleId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\nroleId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/role\", params = {\n    \"token\": systemToken,\n    \"id\": roleId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Role has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Roles",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/role"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/roles",
    "title": "Get Roles",
    "name": "Get_Roles",
    "description": "<p>Get the list of roles.</p>",
    "group": "Roles",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/roles?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/roles\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Roles\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"name\": \"Default\",\n           \"permissions\": [\n               \"default_permissions\"\n           ]\n       },\n       {\n           \"id\": 4,\n           \"name\": \"Rangers\",\n           \"permissions\": [\n               \"manage_users\",\n               \"manage_roles\"\n           ]\n       },\n       {\n           \"id\": 5,\n           \"name\": \"Developer\",\n           \"permissions\": [\n               \"manage_api\"\n           ]\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Roles",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/roles"
      }
    ]
  },
  {
    "type": "post",
    "url": "/update/role",
    "title": "Update Role",
    "name": "Update_Role",
    "description": "<p>Update a user role.</p>",
    "group": "Roles",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Role ID, you can obtain role ID from <strong>/get/roles</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "name",
            "description": "<p>Name of the user role.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "allowedValues": [
              "\"disallow_sms\"",
              "\"disallow_ussd\"",
              "\"disallow_notifications\"",
              "\"disallow_devices\"",
              "\"disallow_wa_chats\"",
              "\"disallow_wa_accounts\"",
              "\"disallow_contacts\"",
              "\"disallow_groups\"",
              "\"disallow_keys\"",
              "\"disallow_webhooks\"",
              "\"disallow_actions\"",
              "\"disallow_templates\"",
              "\"disallow_extensions\"",
              "\"disallow_redeem\"",
              "\"disallow_subscribe\"",
              "\"disallow_topup\"",
              "\"disallow_withdraw\"",
              "\"disallow_convert\"",
              "\"disallow_api\"",
              "\"manage_users\"",
              "\"manage_roles\"",
              "\"manage_packages\"",
              "\"manage_vouchers\"",
              "\"manage_subscriptions\"",
              "\"manage_transactions\"",
              "\"manage_widgets\"",
              "\"manage_pages\"",
              "\"manage_marketing\"",
              "\"manage_languages\"",
              "\"manage_gateways\"",
              "\"manage_shorteners\"",
              "\"manage_plugins\"",
              "\"manage_templates\"",
              "\"manage_api\""
            ],
            "optional": true,
            "field": "permissions",
            "description": "<p>List of permissions separrated by commas.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $role = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"id\" => 2,\n      \"name\" => \"New Role Name\",\n      \"permissions\" => \"disallow_api,manage_users\"\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/update/role\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $role);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nrole = {\n    \"token\": systemToken,\n    \"id\": 2,\n    \"name\": \"New Role Name\",\n    \"permissions\": \"disallow_api,manage_users\"\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/update/role\", params = role)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Role has been updated!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Roles",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/update/role"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/voucher",
    "title": "Create Subscription",
    "name": "Create_Subscription",
    "description": "<p>Create a new subscription.</p>",
    "group": "Subscriptions",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user",
            "description": "<p>User ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "package",
            "description": "<p>Package ID, this can be obtained in <strong>/get/packages</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "duration",
            "description": "<p>Duration of subscription in months.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $subscription = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"user\" => 3,\n      \"package\" => 3,\n      \"duration\" => 3\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/create/subscription\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $subscription);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nsubscription = {\n    \"token\": systemToken,\n    \"user\": 3,\n    \"package\": 3,\n    \"duration\": 3\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/create/subscription\", params = subscription)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Subscription has been created!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Subscriptions",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/voucher"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/subscription",
    "title": "Delete Subscription",
    "name": "Delete_Subscription",
    "description": "<p>Delete an active subscription.</p>",
    "group": "Subscriptions",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Subscription ID, you can obtain a subscription ID from <strong>/get/subscriptions</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $subscriptionId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/subscription?token={$systemToken}&id={$subscriptionId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\nsubscriptionId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/subscription\", params = {\n    \"token\": systemToken,\n    \"id\": subscriptionId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Subscription has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Subscriptions",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/subscription"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/subscriptions",
    "title": "Get Subscriptions",
    "name": "Get_Subscriptions",
    "description": "<p>Get the list of active subscriptions.</p>",
    "group": "Subscriptions",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/subscriptions?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/subscriptions\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Subscriptions\",\n  \"data\": [\n       {\n           \"id\": 2,\n           \"user\": 16,\n           \"package\": 2,\n           \"transaction\": 4,\n           \"created\": 1644202610\n       },\n       {\n           \"id\": 3,\n           \"user\": 6,\n           \"package\": 3,\n           \"transaction\": 6,\n           \"created\": 1645635227\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Subscriptions",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/subscriptions"
      }
    ]
  },
  {
    "type": "get",
    "url": "/clear/cache",
    "title": "Clear Cache",
    "name": "Clear_Cache",
    "description": "<p>Clear system cache files.</p>",
    "group": "System",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/clear/cache?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/clear/cache\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System cache files has been cleared!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "System",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/clear/cache"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/transaction",
    "title": "Delete Transaction",
    "name": "Delete_Transaction",
    "description": "<p>Delete a transaction record.</p>",
    "group": "Transactions",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Transaction ID, you can obtain a transaction ID from <strong>/get/transactions</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $transactionId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/transaction?token={$systemToken}&id={$transactionId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\ntransactionId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/transaction\", params = {\n    \"token\": systemToken,\n    \"id\": transactionId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Transaction has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Transactions",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/transaction"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/transactions",
    "title": "Get Transactions",
    "name": "Get_Transactions",
    "description": "<p>Get the list of transactions.</p>",
    "group": "Transactions",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/transactions?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/transactions\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Transactions\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"user\": 3,\n           \"package\": 0,\n           \"type\": \"credits\",\n           \"price\": 10,\n           \"currency\": \"GBP\",\n           \"duration\": 1,\n           \"provider\": \"paypal\",\n           \"created\": 1644030231\n       },\n       {\n           \"id\": 2,\n           \"user\": 7,\n           \"package\": 4,\n           \"type\": \"package\",\n           \"price\": 100,\n           \"currency\": \"GBP\",\n           \"duration\": 1,\n           \"provider\": \"mollie\",\n           \"created\": 1644030301\n       },\n       {\n           \"id\": 3,\n           \"user\": 9,\n           \"package\": 2,\n           \"type\": \"package\",\n           \"price\": 2400,\n           \"currency\": \"GBP\",\n           \"duration\": 24,\n           \"provider\": \"voucher\",\n           \"created\": 1644059151\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Transactions",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/transactions"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/user",
    "title": "Create Account",
    "name": "Create_Account",
    "description": "<p>Create a new user account.</p>",
    "group": "Users",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Full name of the user.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email address for the user account.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Password for the user account.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "credits",
            "description": "<p>Credits for the user account. Whole number only.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "timezone",
            "description": "<p>Timezone for the user account, here's the list of valid timezones you can use: <a href=\"https://www.w3schools.com/php/php_ref_timezones.asp\">Click Here</a>.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "country",
            "description": "<p>Country code, it should satisfy ISO Alpha-2 format.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "language",
            "description": "<p>Language ID, you can get the a language ID from <strong>/get/languages</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "role",
            "defaultValue": "1",
            "description": "<p>Role ID, you can get a role ID from <strong>/get/roles</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $user = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"name\" => \"Ryan Huggins\",\n      \"email\" => \"mail@domain.com\",\n      \"password\" => \"123456\",\n      \"credits\" => 100,\n      \"timezone\" => \"Asia/Manila\",\n      \"country\" => \"PH\",\n      \"language\" => 1\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/create/user\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $user);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nuser = {\n    \"token\": systemToken,\n    \"name\": \"Ryan Huggins\",\n    \"email\": \"mail@domain.com\",\n    \"password\": \"123456\",\n    \"credits\": 100,\n    \"timezone\": \"Asia/Manila\",\n    \"country\": \"PH\",\n    \"language\": 1\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/create/user\", params = user)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"User has been created!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Users",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/user"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/user",
    "title": "Delete Account",
    "name": "Delete_Account",
    "description": "<p>Delete a user account.</p>",
    "group": "Users",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User ID, you can obtain a user ID from <strong>/get/users</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $userId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/user?token={$systemToken}&id={$userId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\nuserId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/user\", params = {\n    \"token\": systemToken,\n    \"id\": userId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"User has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Users",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/user"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/users",
    "title": "Get Accounts",
    "name": "Get_Accounts",
    "description": "<p>Get the list of user accounts.</p>",
    "group": "Users",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/users?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/users\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Users\",\n  \"data\": [\n       {\n           \"id\": 1,\n           \"role\": 1,\n           \"credits\": 798.132,\n           \"earnings\": 1.9386,\n           \"partner\": true,\n           \"email\": \"mail@domain.com\",\n           \"name\": \"Shane Mondez\",\n           \"country\": \"PH\",\n           \"timezone\": \"asia/manila\",\n           \"language\": 5,\n           \"notification_sounds\": true,\n           \"suspended\": true,\n           \"registered\": 1585646141\n       },\n       {\n           \"id\": 4,\n           \"role\": 3,\n           \"credits\": 50,\n           \"earnings\": 0,\n           \"partner\": true,\n           \"email\": \"test@domain.net\",\n           \"name\": \"Test User\",\n           \"country\": \"KW\",\n           \"timezone\": \"asia/kuwait\",\n           \"language\": 1,\n           \"notification_sounds\": false,\n           \"suspended\": false,\n           \"registered\": 1587452434\n       },\n       {\n           \"id\": 10,\n           \"role\": 1,\n           \"credits\": 0,\n           \"earnings\": 0,\n           \"partner\": false,\n           \"email\": \"test@domain.org\",\n           \"name\": \"Bobby Matthews\",\n           \"country\": \"US\",\n           \"timezone\": \"america/chicago\",\n           \"language\": 3,\n           \"notification_sounds\": true,\n           \"suspended\": false,\n           \"registered\": 1615488072\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Users",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/users"
      }
    ]
  },
  {
    "type": "post",
    "url": "/update/user",
    "title": "Update Account",
    "name": "Update_Account",
    "description": "<p>Update a user account.</p>",
    "group": "Users",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>User ID, you can obtain user ID from <strong>/get/users</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "name",
            "description": "<p>Full name of the user.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "email",
            "description": "<p>Email address for the user account.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "password",
            "description": "<p>Password for the user account.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "credits",
            "description": "<p>Credits for the user account. Whole number only.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "timezone",
            "description": "<p>Timezone for the user account, here's the list of valid timezones you can use: <a href=\"https://www.w3schools.com/php/php_ref_timezones.asp\">Click Here</a>.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "country",
            "description": "<p>Country code, it should satisfy ISO Alpha-2 format.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "language",
            "description": "<p>Language ID, you can get the a language ID from <strong>/get/languages</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "role",
            "description": "<p>Role ID, you can get a role ID from <strong>/get/roles</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $user = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"id\" => 4,\n      \"name\" => \"Ryan Huggins\",\n      \"email\" => \"mail@domain.com\",\n      \"password\" => \"123456\",\n      \"credits\" => 100,\n      \"timezone\" => \"Asia/Manila\",\n      \"country\" => \"PH\",\n      \"language\" => 1\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/update/user\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $user);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nuser = {\n    \"token\": systemToken,\n    \"id\": 4,\n    \"name\": \"Ryan Huggins\",\n    \"email\": \"mail@domain.com\",\n    \"password\": \"123456\",\n    \"credits\": 100,\n    \"timezone\": \"Asia/Manila\",\n    \"country\": \"PH\",\n    \"language\": 1\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/update/user\", params = user)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"User has been updated!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Users",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/update/user"
      }
    ]
  },
  {
    "type": "post",
    "url": "/create/voucher",
    "title": "Create Voucher",
    "name": "Create_Voucher",
    "description": "<p>Create new voucher/s.</p>",
    "group": "Vouchers",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of the voucher/s.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "count",
            "description": "<p>Number of vouchers to create.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "duration",
            "description": "<p>Duration of subscription in months.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "package",
            "description": "<p>Package ID, this can be obtained in <strong>/get/packages</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $voucher = [\n      \"token\" => \"SYSTEM_TOKEN\", // system token, can be acquired from system settings.\n      \"name\" => \"Amazing Voucher\",\n      \"count\" => 10,\n      \"duration\" => 3,\n      \"package\" => 3\n  ];\n\n  $cURL = curl_init(\"http://127.0.0.1/zender/system/create/voucher\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  curl_setopt($cURL, CURLOPT_POSTFIELDS, $voucher);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nvoucher = {\n    \"token\": systemToken,\n    \"name\": \"Amazing Voucher\",\n    \"count\": 10,\n    \"duration\": 3,\n    \"package\": 3\n}\n\nr = requests.post(url = \"http://127.0.0.1/zender/system/create/voucher\", params = voucher)\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Voucher has been created!\",\n  \"data\": [\n       {\n           \"code\": \"314a0fdf80bb61247304b8b1e6f023ab\",\n           \"name\": \"Amazing Voucher\",\n           \"package\": 3,\n           \"duration\": 1\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Vouchers",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/create/voucher"
      }
    ]
  },
  {
    "type": "get",
    "url": "/delete/voucher",
    "title": "Delete Voucher",
    "name": "Delete_Voucher",
    "description": "<p>Delete an unused voucher.</p>",
    "group": "Vouchers",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Voucher ID, you can obtain a voucher ID from <strong>/get/vouchers</strong></p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $voucherId = 4;\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/delete/voucher?token={$systemToken}&id={$voucherId}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\nvoucherId = 6\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/delete/voucher\", params = {\n    \"token\": systemToken,\n    \"id\": voucherId\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Voucher has been deleted!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Vouchers",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/delete/voucher"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get/vouchers",
    "title": "Get Vouchers",
    "name": "Get_Vouchers",
    "description": "<p>Get the list of unused vouchers.</p>",
    "group": "Vouchers",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
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
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/get/vouchers?token={$systemToken}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/get/vouchers\", params = {\n    \"token\": systemToken\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"System Vouchers\",\n  \"data\": [\n       {\n           \"id\": 27,\n           \"package\": 4,\n           \"duration\": 1,\n           \"name\": \"1k Voucher #1\",\n           \"code\": \"caf1fbc8191d3bdfe736f5b6a36011cf\",\n           \"created\": 1605869831\n       },\n       {\n           \"id\": 28,\n           \"package\": 4,\n           \"duration\": 1,\n           \"name\": \"1k Voucher #2\",\n           \"code\": \"b7c289d2b0e69c5ee0791c966650e8ca\",\n           \"created\": 1605869831\n       },\n       {\n           \"id\": 29,\n           \"package\": 4,\n           \"duration\": 1,\n           \"name\": \"1k Voucher #3\",\n           \"code\": \"062a0cdc6faf8a3fa91aa572495c861f\",\n           \"created\": 1605869831\n       }\n   ]\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Vouchers",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/get/vouchers"
      }
    ]
  },
  {
    "type": "get",
    "url": "/redeem",
    "title": "Redeem Voucher",
    "name": "Redeem_Voucher",
    "description": "<p>Redeem an unused voucher.</p>",
    "group": "Vouchers",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>System token, can be acquired from system settings.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user",
            "description": "<p>User ID, you can obtain a user ID from <strong>/get/users</strong></p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>Voucher Code</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "PHP Example",
        "content": "<?php\n\n  $systemToken = \"SYSTEM_TOKEN\"; // system token, can be acquired from system settings.\n  $userId = 4;\n  $voucherCode = \"caf1fbc8191d3bdfe736f5b6a36011cf\";\n\n  $cURL = curl_init();\n  curl_setopt($cURL, CURLOPT_URL, \"http://127.0.0.1/zender/system/redeem?token={$systemToken}&user={$userId}&code={$voucherCode}\");\n  curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);\n  $response = curl_exec($cURL);\n  curl_close($cURL);\n\n  $result = json_decode($response, true);\n\n  // do something with response\n  print_r($result);",
        "type": "php"
      },
      {
        "title": "Python Example",
        "content": "import requests\n\n# system token, can be acquired from system settings.\nsystemToken = \"SYSTEM_TOKEN\"\nuserId = 6\nvoucherCode = \"caf1fbc8191d3bdfe736f5b6a36011cf\"\n\nr = requests.get(url = \"http://127.0.0.1/zender/system/redeem\", params = {\n    \"token\": systemToken,\n    \"user\": userId,\n    \"code\": voucherCode\n})\n  \n# do something with response object\nresult = r.json()",
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
          "content": "{\n  \"status\": 200,\n  \"message\": \"Voucher has been redeemed!\",\n  \"data\": false\n}",
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
    "filename": "system/controllers/admin.php",
    "groupTitle": "Vouchers",
    "sampleRequest": [
      {
        "url": "http://127.0.0.1/zender/api/redeem"
      }
    ]
  }
] });
