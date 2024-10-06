<style>
  #map, #Maps-for-FUNdraising, #map2, #map3, #map4 {
    height: 575px;
    width: 100%;
  }
  
  .loader {
    border: 8px solid #f3f3f3;
    border-radius: 50%;
    border-top: 8px solid #3498db;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    position: absolute;
    top: 25%;
    left: 50%;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

<script async src="<?php echo asset_url('assets/js/google-maps.js'); ?>"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1742C-IzptNBXxafjlHHJW2rvyAleQ-I&loading=async&callback=initMaps"></script>
<!-- Include the MarkerClusterer library -->
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

<script>
  let mapData;
  // Fetch the JSON file
    //fetch("<?php //echo asset_url('assets/maps.json'); ?>")
    const urlParams = new URLSearchParams(window.location.search);
    const mapName = urlParams.get('name');

    fetch(window.location.origin + "/maps/map_data?name=" + mapName)
    .then(response => {
      // Check if the response is OK
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      // Clone the response before parsing JSON data
      return response.clone().json();
    })
    .then(data => {
      mapData = data.mapOptions[0];
      // Once JSON data is fetched and parsed, you can work with it
      // console.log(data); // This will log the parsed JSON data to the console
      // Example usage:
      // Object.keys(data).forEach((mapKey, index) => {
      //   const map = data[mapKey];
      //   //console.log("Title: " + map.title);
      //   //console.log("Center: " + map.options.center.lat + ", " + map.options.center.lng);
      //   console.log("Map ID: " + map[index].mapId);
      //   console.log("----------------------------------------");
      // });
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation:', error);
    });
</script>

<script>
  // Define the initMaps function
  function initMaps(mapData) {
    if (typeof mapData !== 'undefined') {
      const elemId = mapData.elemId;
      const divElement = '<div class="col mb-4">' +
        '<h1 class="text-center mb-2">' + elemId + '</h1>' +
        '<div class="maps" id="' + elemId + '"></div></div>';
      document.getElementById('rowContainer').innerHTML += divElement;

      // Initialize the map
      initMap(mapData);

      // Hide loader when mapData is defined
      document.getElementById('loader').style.display = 'none';
    }
  }

  // Call initMaps function after a delay
  setTimeout(function() {
    // Check if initMap is defined
    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.Map === 'function' && mapData !== null) {
      initMaps(mapData); // Call initMaps instead of initializeMaps
    } else {
      console.error('Google Maps API is not loaded or initialized.');
    }
  }, 2000);
</script>

<content class="content">
  <div class="container">
    <div class="row" id="rowContainer"></div>
    <div id="loader" class="loader"></div>
  </div>
</content>