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
 * @param {string} fileToSignAbsolutePath - The path to the PDF document to sign. 
 * Example : __dirname + '/document1.pdf'
 * @param {string} firstName - Firstname of the person that will sign
 * @param {string} lastName - Lastname of the person that will sign
 * @param {string} email - Email of the person that will sign
 * @param {string} phone - Phone of the person that will sign, including prefix (+33...). 
 * Must be a real Phone number as the user will receive an SMS confirmation code.
 * @param {string} signatureCoordinates - Pixel coordinates of the rectangle where the signature will
 *  appear on the document. Example : 351,32,551,132
 * @param {string} userSuccessRedirectUrl - A url where the user will be redirected to after he signs
 * @param {string} userCancelRedirectUrl - A url where the user will be redirected to after he cancels
 *  the signature process
 * @param {string} onSignatureStatusChangedUrl - The YouSign server will send GET requests to this url
 *  when the signature status changes. Statuses can be : init, cancel, waiting, signed, signed_complete
 * @returns {*} promise - a promise that resolves to an object containing : 
 * {string} iframeUrl - the url of the iframe to do the signature 
 * {object} details - details of the signature, contains de demand ID as well as the signature token
 * which can be used later on to match the token sent to onSignatureStatusChangedUrl by YouSign
 */
function initSignature(fileToSignAbsolutePath, firstname, lastname, email, phone,
    signatureCoordinates, userSuccessRedirectUrl, userCancelRedirectUrl, onSignatureStatusChangedUrl) {
    return new Promise((resolve, reject)=>{
        var command = ["php", "initSignature.php"].concat(Array.from(arguments))
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