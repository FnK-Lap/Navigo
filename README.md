# Navigo
![Build
Status](https://travis-ci.org/FnK-Lap/Navigo.svg?branch=master)

## Installation

    $ composer install

# Utilisation de la Borne
## Creation d'un client
`POST`: `/client?redirect_uri=YOUR_REDIRECT_URI`

Reponse:
```
{
  "status": 201,
  "message": "Success",
  "errors": "",
  "client": {
    "client_id": "6_56538hlfrn8cco0sgc88cowossg0wk4o48cg404k0soo0ww0kc",
    "client_secret": "1dmiiviga5okgww8cskoc0c0swcc4wc0kw0wg480gw4gsosgoo"
  }
}
```

## Recuperation du Code
`GET`: `/oauth/v2/auth?client_id=CLIENT_ID&response_type=code&redirect_uri=YOUR_REDIRECT_URI`

Redirect to: 
`YOUR_REDIRECT_URI/?code=ZDZmZmUzNzAzMzZiMTJhOWE1Yjc1MzFhODdlMjM3OTMwYmZhM2ZmNTAzYWI1N2VkZDk3ZjVjZmZkZDE0MDc0Mw`

## Recuperation du token
`GET`: `oauth/v2/token?client_id=CLIENT_ID&client_secret=CLIENT_SECRET&grant_type=authorization_code&redirect_uri=YOUR_REDIRECT_URI&code=CODE`

Reponse:
```
{
    "access_token": "ZTA3YTc3MzI2NzYxMWE3ZjY1Nzg5MDViMmE5ZmU1YWFiODgxYTc5YjRjMWU3ZGFhOTMxZmYwZGRmNzdmMWVlYQ",
    "expires_in": 3600,
    "token_type": "bearer",
    "scope": null,
    "refresh_token": "OGQ5N2Y4YzA4MmE2NmIzNGFiZDk2ODRkNDk5NTg0OWVmNDE1N2FhNzViN2M3N2I3M2UzNWJiYmUzZjQ1OWI4NA"
}
```

## Valider une carte
`POST`:`api/card/validate?access_token=ACCESS_TOKEN` with params `serial_number`
