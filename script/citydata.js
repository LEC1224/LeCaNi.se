function fetchCityData(cityName) {
  fetch(`cityContent/${cityName}.json`)
    .then(response => response.json())
    .then(city => {
      const container = document.getElementById(`city-${cityName}`);
      
      // Creating the city title <h2>
      const cityTitle = document.createElement('h2');
      cityTitle.textContent = city.name;
      
      // Insert the title before the slideshow
      const slideshow = container.querySelector('.slideshow-container');
      container.insertBefore(cityTitle, slideshow);
      
      // Creating the attributes list
      const cityList = document.createElement('ul');
      cityList.className = "city-list";

      const attributes = [
        {label: "Grundare", value: city.founder},
        {label: "Grundades", value: city.yearFounded},
        {label: "Storlek", value: city.size},
        {label: "Biom", value: city.biome},
        {label: "Kust", value: city.coast},
        {label: "Mur", value: city.wall},
        {label: "Koordinater", value: city.coords}
      ];

      attributes.forEach(attr => {
        const li = document.createElement('li');
        li.textContent = `${attr.label}: ${attr.value}`;
        cityList.appendChild(li);
      });
      
      // Insert the title before the slideshow
      const appendix = container.querySelector('.city-appendix');
      container.insertBefore(cityList, appendix);

      const descriptionParagraphs = city.description.split("\n");
      for(const paragraph of descriptionParagraphs) {
        const cityDescription = document.createElement('p');
        cityDescription.textContent = paragraph;
        container.insertBefore(cityDescription, appendix);
      }
    })
    .catch(error => {
      console.error('Error fetching the JSON:', error);
    });
}