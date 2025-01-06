var quoteList;

async function load() {
	quoteList = new Array();
	await getQuotes();
	var quoteNumber = quoteList.length-1;
	let subtitle = "Your favorite state-of-the-art TrackMania motivational quote generator<br>with more than " + (quoteNumber-1) + " quotes!!!";
	document.getElementById("subtitle").innerHTML = subtitle;
	document.getElementById("subsubtitle").innerHTML = "(It's " + quoteNumber + " quotes)";
}

async function generate() {
	var index = (Math.random() * (quoteList.length-1));
	index = Math.floor(index);
	var quoteText = quoteList[index];
	document.getElementById("quoteText").innerHTML = quoteText;
}

async function getQuotes() {
	var path = "quotes.txt";
	var unprocessedString;
	let response = await fetch(path);
	if (response.ok) {
		unProcessedString = await response.text();
	} else {
		alert("Didn't find document");
	}
	var lines = unProcessedString.split("\n");
	var i = 0;
	for (let j = 0; j < lines.length; j++) {
		if(lines[j].length <= 1) {
			quoteList[i] = quoteList[i].substring(9, quoteList[i].length-4)
			i++;
		} else {
			quoteList[i] += lines[j] + "<br>";
		}
	}
}
function copy() {
	var copyText = document.getElementById("quoteText").innerHTML;
	copyText = copyText.replace("<br>", '');
	navigator.clipboard.writeText(copyText);
}

/*function submitText() {
	Email.send({
        Host: "smtp.gmail.com",
        Username: "lcn.minerC1224@gmail.com",
        Password: "Never",
        To: 'lecanistuff@mail.com',
        From: "lcn.minerC1224@gmail.com",
        Subject: "Sending Email using javascript",
        Body: "Well that was easy!!",
      });
}*/