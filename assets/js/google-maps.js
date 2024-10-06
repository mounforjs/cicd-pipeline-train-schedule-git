var map
const markers = []; // Define markers as an empty array

async function initMap(mapData) {
  if (mapData) {
    var options = {
      "mapId": mapData.mapId,
      "center": {"lat": parseFloat(mapData.CenterLat), "lng": parseFloat(mapData.CenterLon)},
      "zoom": parseFloat(mapData.zoom),
      "mapTypeId": mapData.mapTypeId,
      "disableDefaultUI": mapData.disableDefaultUI,
    }

    try {
      var id = mapData.elemId;
      var myOptions = options;

      // Load the Maps and Advanced Markers library asynchronously
      const { Map } = await google.maps.importLibrary("maps");
      const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
      const map = new Map(document.getElementById(id), myOptions); 

      // Function to create an image element
      function createImageElement(src) {
        const imgElement = document.createElement('img');
        imgElement.src = src;
        return imgElement;
      }

      for (let i = 0; i < mapData.markerData.length; i++) {
          const marker = new AdvancedMarkerElement({
              map,
              position: {
                  lat: parseFloat(mapData.markerData[i].markerLatitude),
                  lng: parseFloat(mapData.markerData[i].markerLongitude)
              },
              content: createImageElement(mapData.markerData[i].content), // Create the image element dynamically
              title: mapData.markerData[i].title,
          });
      
          const infoWindowData = mapData.markerData[i].markerInfoWindowData; // Access infoWindowData of current marker
      
          if (infoWindowData !== undefined) {
            // Create separate infowindow instances for each marker
            const infowindow = new google.maps.InfoWindow({
                content: infoWindowData.content,
                maxWidth: infoWindowData.maxWidth,
                minWidth: infoWindowData.minWidth,
                maxHeight: infoWindowData.maxHeight,
                minHeight: infoWindowData.minHeight,
                pixelOffset: infoWindowData.pixelOffset,
                position: infoWindowData.position,
                autoClose: infoWindowData.autoClose
            });
    
            // Add a click listener to each marker to open the infowindow
            marker.addListener("click", () => {
                infowindow.open({
                    anchor: marker,
                    map,
                });
            });
          }

          //Push the marker into the selectedMarkers array if it's the third or fourth marker
          if (mapData.markerClusterList.includes(mapData.markerData[i].markerList)) {
             markers.push(marker);
          }
      }
      const markerCluster = new markerClusterer.MarkerClusterer({ markers , map });   
    } catch (error) {
      console.error('Error loading Advanced Markers library:', error);
    }
  }
}
