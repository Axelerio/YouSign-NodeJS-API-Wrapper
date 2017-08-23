var exec = require("child_process").exec;

// Useful for returning well-formatted errors
function buildError(){
    return {success: false, errors: Array.from(arguments)}
}

/**
 * @desc Checks if the YouSign API is up and if our credentials are valid
 */
function checkAuth() {
    return new Promise((resolve, reject)=>{
        var command = ["php", "checkAuth.php"]
        exec(command.join(' '), { cwd: __dirname }, (error, stdout, stderr)=>{
            if(!error && !stderr){
                try{
                    var result = JSON.parse(stdout)
                } catch(e){
                    var err = "checkAuth - could not parse result: " + stdout
                    console.error(err)
                }
                if(err){
                    return reject(buildError(err))
                } else if(result.success != true){
                    return reject(result)
                } else {
                    return resolve(result)
                }
            } else {
                return reject(buildError(error, stderr))
            }
        })
    })
}

/**
 * @desc Inits a signature and returns the url of the signing page 
 * @param {array} filesToSign - An array of absolute paths to the documents that you wish to sign. 
 * Example : [/Users/joe/document1.pdf, /Users/joe/document2.pdf]
 * Alternatively, you can send a string if you only have one file to sign
 * Example : document1.pdf
 * @param {string} firstName - Firstname of the person that will sign
 * @param {string} lastName - Lastname of the person that will sign
 * @param {string} email - Email of the person that will sign
 * @param {string} phone - Phone of the person that will sign, including prefix (+33...). 
 * Must be a real Phone number as the user will receive an SMS confirmation code.
 * @param {array} signatures - An array of objects, each object containing the following data for a signature : 
 * page, document number, pixel coordinates of the rectangle where it will appear. Example for two signatures on page 2 and 4
 * on the first document of the filesToSign, and on page 2 of the second document:
 * [{rectangleCoords: "337,59,572,98", page:"2", document:"1"}, {rectangleCoords: "337,193,572,232", page:"4", document:"1"},
 * {rectangleCoords: "100,200,300,400", page:"2", document:"2"}],
 * If you only have one signature, and want to put on page 1 of first document, you can send only a string of coordinates
 * instead of an array : "337,59,572,98"
 * @param {string} userSuccessRedirectUrl - A url where the user will be redirected to after he signs
 * @param {string} userCancelRedirectUrl - A url where the user will be redirected to after he cancels
 *  the signature process
 * @param {string} onSignatureStatusChangedUrl - The YouSign server will send GET requests to this url
 *  when the signature status changes. Statuses can be : init, cancel, waiting, signed, signed_complete
 * @returns {string} iframeUrl - the url of the iframe to do the signature
 * @returns {object} details - details of the signature, contains de demand ID which can be used later on
 */
function initSignature(filesToSign, firstname, lastname, email, phone,
    signatures, userSuccessRedirectUrl, userCancelRedirectUrl, onSignatureStatusChangedUrl) {
    return new Promise((resolve, reject)=>{

        //Array of signature objects instead of one string
        if(typeof signatures == 'object'){
            var signatureList = []
            signatures.forEach((signature)=>{
                signatureList.push(signature.rectangleCoords + 
                "-" + signature.page + 
                "-" + (signature.document ? signature.document : 0))
            })
            signatures = signatureList.join("_")
        }

        //Array of file paths instead of one string
        if(typeof filesToSign == 'object'){
            filesToSign = filesToSign.join("[]_THIS_IS_A_BIG_SEPARATOR_[]")
        }

        //Base64 encode arguments so that we don't send invalid chars in shell
        var args = Array.from(arguments)
        var encodedArguments = []
        args.forEach((argument)=>{
            encodedArguments.push(Buffer.from(argument).toString('base64'))
        })
        var command = ["php", "initSignature.php"].concat(encodedArguments)
        if(command.length < 8){
            return reject(buildError("Missing parameters"))
        }
        exec(command.join(' '), { cwd: __dirname }, (error, stdout, stderr)=>{
            if(!error && !stderr){
                try{
                    var result = JSON.parse(stdout)
                } catch(e){
                    var err = "initSignature - could not parse result: " + stdout
                    console.error(err)
                }
                if(err){
                    return reject(buildError(err))
                } else if(result.success != true){
                    return reject(result)
                } else {
                    //Add the redirect and callback urls to the signature page url as GET parameters
                    var suffix = "?urlsuccess=" + encodeURIComponent(userSuccessRedirectUrl) +
                        "&urlcancel=" + encodeURIComponent(userCancelRedirectUrl) +
                        "&urlcallback=" + encodeURIComponent(onSignatureStatusChangedUrl)
                    result.signingUrl = result.signingUrl + suffix
                    return resolve(result)
                }
            } else {
                return reject(buildError(error, stderr))
            }
        })
    })
}

/**
 * @desc Lists the existing signatures and the corresponding statuses for an email
 * @param {string} email - Email of the person whose signatures we want to get
 */
function listSignatures(email) {
    return new Promise((resolve, reject)=>{
        var command = ["php", "listSignatures.php"].concat(Array.from(arguments))
        if(command.length < 3){
            return reject(buildError("Missing parameters"))
        }
        exec(command.join(' '), { cwd: __dirname }, (error, stdout, stderr)=>{
            if(!error && !stderr){
                try{
                    var result = JSON.parse(stdout)
                } catch(e){
                    var err = "listSignature - could not parse result: " + stdout
                    console.error(err)
                }
                if(err){
                    return reject(buildError(err))
                } else if(result.success != true){
                    return reject(result)
                } else {
                    return resolve(result)
                }
            } else {
                return reject(buildError(error, stderr))
            }
        })
    })
}

// Public functions export
module.exports = {
    initSignature: initSignature,
    checkAuth: checkAuth,
    listSignatures: listSignatures
}

// Example call :
/*listSignatures("jean@dubois.org").then((res)=>{
    console.log(res)
}).catch((err)=>{
    console.error(err)
})*/