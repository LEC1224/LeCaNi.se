const toggleButton = document.getElementById('toggleButton');

// Remove any existing click event listeners
toggleButton.onclick = null;

// Attach the new click event listener
toggleButton.addEventListener('click', function() {
  const content = document.getElementById('hidden_content_news_id');
  content.classList.toggle('show');
  if (content.style.maxHeight && content.style.maxHeight !== '0px') {
    content.style.maxHeight = '0px';
	toggleButton.innerHTML = "Visa gamla nyheter...";
  } else {
    content.style.maxHeight = content.scrollHeight + 'px';
	toggleButton.innerHTML = "Göm gamla nyheter...";
  }
});