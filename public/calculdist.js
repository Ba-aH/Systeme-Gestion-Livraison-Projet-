function calculateDistance(lat1, lon1, lat2, lon2) {
  // Convert degrees to radians
  const earthRadius = 6371e3; // meters (change units if desired)
  const radiansLat1 = deg2rad(lat1);
  const radiansLat2 = deg2rad(lat2);
  const dLat = radiansLat2 - radiansLat1;
  const dLon = deg2rad(lon2 - lon1);

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(radiansLat1) * Math.cos(radiansLat2) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return earthRadius * c;
}

function deg2rad(deg) {
  return deg * (Math.PI / 180);
}