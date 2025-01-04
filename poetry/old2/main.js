async function load() {
	const params = new URLSearchParams(window.location.search);
	var poemName = params.get("n");
	
	var path = "text/" + poemName + ".txt"
	path = path.toLowerCase();
	var poemText = "wow such empty";
	let response = await fetch(path);
	if (response.ok) { // if HTTP-status is 200-299
		// get the response body (the method explained below)
		poemText = await response.text();
	} else {
		alert("HTTP-Error: " + response.status);
	}
	poemText = poemText.replaceAll("\n", "<br />");
	poemText = "<p style='font-size: 0.7cm; font-family: Times; font-style: italic;'>" + poemText + "</p>";
	
	document.getElementById("poem").innerHTML = poemText;
	
	var poemTitle = poemName;
	poemTitle = poemTitle.replaceAll("_", " ");
	poemTitle = poemTitle.substring(0, 1).toUpperCase() + poemTitle.substring(1);
	
	//To remove folder from path
	if(poemTitle.includes('/')) {
		splitList = poemTitle.split("/");
		poemTitle = splitList[1];
	}
	
	document.getElementById("poemTitle").innerHTML = poemTitle;
}
