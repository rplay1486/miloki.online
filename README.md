#  miloki.online

API PHP para convertir ClearKey KID y KEY desde HEX
a Base64U`RL y devolverlos en formato JSON.

## Endpoint

/api/results.php

## Parámetros

keyid = Key ID en HEX

key = Key en HEX

## Ejemplo

/api/results.php?keyid=00112233445566778899aabbccddeeff&key=ffeeddccbbaa99887766554433221100

## Respuesta

{
    "keys": [
        {
            "kty": "oct",
            "k": "...",
            "kid": "..."
        }
    ],
    "type": "temporary"
}

## Licencia

Este proyecto solamente realiza una conversión de formato.
