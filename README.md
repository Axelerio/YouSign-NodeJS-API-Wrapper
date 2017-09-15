# YouSign-NodeJS-API-Wrapper

A Node.js API client for the YouSign signature API. Works as a wrapper around the Yousign PHP API client

The original PHP Api : https://github.com/Yousign/yousign-api-client-php
## Usage:

1. Get a recent Node.js version (with ES6 support)
2. Install PHP (version 7+ recommended). Make it so that you can run php by typing "php" in terminal
3. Install this module into your project
```bash
npm i yousign-nodejs-api-wrapper --save
```
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
<dt><a href="#initSignature">initSignature(filesToSign, firstName, lastName, email, phone, signatures, userSuccessRedirectUrl, userCancelRedirectUrl, onSignatureStatusChangedUrl)</a> ⇒ <code>string</code> | <code>object</code></dt>
<dd><p>Inits a signature and returns the url of the signing page</p>
</dd>
<dt><a href="#downloadSignaturesFiles">downloadSignaturesFiles(search, absoluteOutFolderPath)</a></dt>
<dd><p>Downloads all the files in the signature process corresponding to the search parameter provided</p>
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

## initSignature(filesToSign, firstName, lastName, email, phone, signatures, userSuccessRedirectUrl, userCancelRedirectUrl, onSignatureStatusChangedUrl) ⇒ <code>string</code> \| <code>object</code>
Inits a signature and returns the url of the signing page

**Kind**: global function
**Returns**: <code>string</code> - iframeUrl - the url of the iframe to do the signature<code>object</code> - details - details of the signature, contains de demand ID which can be used later on

| Param | Type | Description |
| --- | --- | --- |
| filesToSign | <code>array</code> | An array of absolute paths to the documents that you wish to sign.  Example : [/Users/joe/document1.pdf, /Users/joe/document2.pdf] Alternatively, you can send a string if you only have one file to sign Example : document1.pdf |
| firstName | <code>string</code> | Firstname of the person that will sign |
| lastName | <code>string</code> | Lastname of the person that will sign |
| email | <code>string</code> | Email of the person that will sign |
| phone | <code>string</code> | Phone of the person that will sign, including prefix (+33...).  Must be a real Phone number as the user will receive an SMS confirmation code. |
| signatures | <code>array</code> | An array of objects, each object containing the following data for a signature :  page, document number, pixel coordinates of the rectangle where it will appear. Example for two signatures on page 2 and 4 on the first document of the filesToSign, and on page 2 of the second document: [{rectangleCoords: "337,59,572,98", page:"2", document:"1"}, {rectangleCoords: "337,193,572,232", page:"4", document:"1"}, {rectangleCoords: "100,200,300,400", page:"2", document:"2"}], If you only have one signature, and want to put on page 1 of first document, you can send only a string of coordinates instead of an array : "337,59,572,98" |
| userSuccessRedirectUrl | <code>string</code> | A url where the user will be redirected to after he signs |
| userCancelRedirectUrl | <code>string</code> | A url where the user will be redirected to after he cancels  the signature process |
| onSignatureStatusChangedUrl | <code>string</code> | The YouSign server will send GET requests to this url  when the signature status changes. Statuses can be : init, cancel, waiting, signed, signed_complete |

<a name="downloadSignaturesFiles"></a>

## downloadSignaturesFiles(search, absoluteOutFolderPath)
Downloads all the files in the signature process corresponding to the search parameter provided

**Kind**: global function

| Param | Type | Description |
| --- | --- | --- |
| search | <code>string</code> | The YouSign search parameters. This can be the email of the person who signed, the filename... Example : john@test.com - will download all the files in a signature process with john@test.com |
| absoluteOutFolderPath | <code>string</code> | An absolute path to the output folder for the downloaded files (without trailing /) Example : /Users/joe/out - the folder MUST already exist and be writeable |

<a name="listSignatures"></a>

## listSignatures(email)
Lists the existing signatures and the corresponding statuses for an email

**Kind**: global function

| Param | Type | Description |
| --- | --- | --- |
| email | <code>string</code> | Email of the person whose signatures we want to get |