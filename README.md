# YouSign-NodeJS-API-Wrapper

A Node.js API client for the YouSign signature API. Works as a wrapper around the Yousign PHP API client

The original PHP Api : https://github.com/Yousign/yousign-api-client-php
## Usage:

1. Install Node.js (recent version with ES6 support)
2. Install PHP (version 7+ recommended)
3. Clone the repo
4. Copy example_ysApiParameters.ini in the module's folder into your project root, rename it to ysApiParameters.ini
5. Set your Yousign credentials into the file. Please note that if isEncryptedPassword = true your password must be in the following format : **sha1(sha1(YOUR_PASSWORD)+sha1(YOUR_PASSWORD))** (with + being a concat operator). Otherwise set the boolean to false and just leave it as clear text. But that's bad mkay.
6. Import the index.js module into your project
7. Call the checkAuth() function (all functions are promise-based, so you can do .then and .catch on it

## How to list signatures:
```JavaScript
var ysAPIWrapper = require('yousign-nodejs-api-wrapper')

ysAPIWrapper.listSignatures("jean@dubois.org").then((res)=>{
    console.log(res)
}).catch((err)=>{
    console.error(err)
})
```



## Available functions :

<dl>
<dt><a href="#checkAuth">checkAuth()</a></dt>
<dd><p>Checks if the YouSign API is up and if our credentials are valid</p>
</dd>
<dt><a href="#initSignature">initSignature(fileToSignRelativePath, firstName, lastName, email, phone, signatureCoordinates)</a></dt>
<dd><p>Inits a signature and returns the url of the signing page</p>
</dd>
<dt><a href="#listSignatures">listSignatures(email)</a></dt>
<dd><p>Lists the existing signatures and the corresponding statuses for an email</p>
</dd>
</dl>

<a name="checkAuth"></a>

## checkAuth()
Checks if the YouSign API is up and if our credentials are valid

**Kind**: global function
<a name="initSignature"></a>

## initSignature(fileToSignRelativePath, firstName, lastName, email, phone, signatureCoordinates)
Inits a signature and returns the url of the signing page

**Kind**: global function

| Param | Type | Description |
| --- | --- | --- |
| fileToSignRelativePath | <code>string</code> | The path to the PDF document to sign, relative to the module folder.  Example : document1.pdf |
| firstName | <code>string</code> | Firstname of the person that will sign |
| lastName | <code>string</code> | Lastname of the person that will sign |
| email | <code>string</code> | Email of the person that will sign |
| phone | <code>string</code> | Phone of the person that will sign, including prefix (+33...).  Must be a real Phone number as the user will receive an SMS confirmation code. |
| signatureCoordinates | <code>string</code> | Pixel coordinates of the rectangle where the signature will appear on the document. Example : 351,32,551,132 |

<a name="listSignatures"></a>

## listSignatures(email)
Lists the existing signatures and the corresponding statuses for an email

**Kind**: global function

| Param | Type | Description |
| --- | --- | --- |
| email | <code>string</code> | Email of the person whose signatures we want to get |