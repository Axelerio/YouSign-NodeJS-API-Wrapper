//var ysAPIWrapper = require('yousign-nodejs-api-wrapper')
var ysAPIWrapper = require('./index.js')

var doSignature = (fileToSignRelativePath,
                   firstname,
                   lastname,
                   email,
                   phone,
                   signatureCoordinates,
                   userRedirectUrl,
                   signatureSuccessCallbackUrl, 
                   signatureCancelCallbackUrl)=>{
    return ysAPIWrapper.initSignature(fileToSignRelativePath, firstname, lastname, email, phone, signatureCoordinates).then((result)=>{
        if(result && result.signingUrl){
            return result
        }
    })
}

doSignature("document1.pdf",
     "Guy",
     "Test",
     "guy.test@testmail.com",
     "+33601010101",
     "100,100,100,100",
     "http://example.com/greetCustomerAfterSignature",
     "http://example.com/onSignatureStatusChanged",
     "http://example.com/onSignatureCanceled").then((result)=>{
    console.log("DONE", JSON.stringify(result, null, 2))
}).catch((err)=>{
    console.error("ERROR", err)
})