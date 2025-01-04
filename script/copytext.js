function copyText(inputText, source) {
  
    const text = source.getText();
    
    // Create a textarea element to hold the text
    var textarea = document.createElement("textarea");
    textarea.textContent = inputText;
    
    // Append the textarea to the body
    document.body.appendChild(textarea);
    
    // Select the text
    textarea.select();
    
    try {
        // Copy the text
        document.execCommand('copy');
        
        // Change the color of the button and the text
        source.classList.add("green");
        source.textContent = "| Kopierat! |";
		
		setTimeout(function() {
            source.classList.remove("green");
            source.textContent = text;
        }, 2000);
    } catch (err) {
        console.log('Failed to copy text: ', err);
    } finally {
        // Remove the textarea from the body
        document.body.removeChild(textarea);
    }
}