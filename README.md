# YouSign-NodeJS-API-Wrapper

A Node.js API client for the YouSign signature API. Works as a wrapper around the Yousign PHP API client
## Usage:

1. Install Node.js (recent version with ES6 support)
2. Install PHP (version 7+ recommended)
3. Clone the repo
4. Rename example_ysApiParameters.ini to ysApiParameters.ini
5. Set your Yousign credentials into the file. Please note that if isEncryptedPassword = true your password must be in the following format : **sha1(sha1(YOUR_PASSWORD)+sha1(YOUR_PASSWORD))** (with + being a concat operator). Otherwise set the boolean to false and just leave it as clear text. But that's bad mkay.
6. Import the index.js module into your project
7. Call the checkAuth() function (all functions are promise-based, so you can do .then and .catch on it

## How to list signatures:
```JavaScript
var ysAPIWrapper = require('./index.js')

ysAPIWrapper.listSignatures("jean@dubois.org").then((res)=>{
    console.log(res)
}).catch((err)=>{
    console.error(err)
})
```
## Available functions :

1. initSignature
2. checkAuth
3. listSignatures

## The full documentation is available at jsdoc/index.html

**The original PHP Api : https://github.com/Yousign/yousign-api-client-php**