document.getElementById("copyButton").addEventListener("click", function() {
    
	var btn = this;
	// Get the text
    var text = document.getElementById("textToCopy").textContent;
    
    // Create a textarea element to hold the text
    var textarea = document.createElement("textarea");
    textarea.textContent = text;
    
    // Append the textarea to the body
    document.body.appendChild(textarea);
    
    // Select the text
    textarea.select();
    
    try {
        // Copy the text
        document.execCommand('copy');
        
        // Change the color of the button and the text
        this.classList.add("green");
        this.textContent = "Copied!";
		
		setTimeout(function() {
            btn.classList.remove("green");
            btn.textContent = "IP: mc.lecani.se";
        }, 2000);
    } catch (err) {
        console.log('Failed to copy text: ', err);
    } finally {
        // Remove the textarea from the body
        document.body.removeChild(textarea);
    }
});