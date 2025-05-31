// map/scripts.js
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Peta tidak ditemukan. Pastikan elemen #map ada di HTML.');
        return;
    }

    var map = L.map('map').setView([-6.31003, 106.75752], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let userMarker = null;
    let locations = []; 
    let searchMarkers = L.featureGroup().addTo(map); 
    let currentRoutePolyline = null; 

    const ORS_API_KEY = '5b3ce3597851110001cf6248cc1939b219bf43db8634e6730b57d55b'; // GANTI DENGAN API KEY ANDA YANG VALID
    const JABODETABEK_BBOX = '106.5,-6.5,107.2,-5.9'; 

    const originSelect = document.getElementById('origin');
    const destinationSelect = document.getElementById('destination');
    const searchInput = document.getElementById('searchLocation');

    function normalizeString(str) {
        if (typeof str !== 'string') return '';
        let normalizedStr = str.toLowerCase();
        normalizedStr = normalizedStr.replace(/\bjl\.\b|\bjln\.\b/g, 'jalan');
        normalizedStr = normalizedStr.replace(/\bkh\.\b/g, 'kyai haji');
        normalizedStr = normalizedStr.replace(/\bh\.\b/g, 'haji');
        normalizedStr = normalizedStr.replace(/\bhj\.\b/g, 'hajjah');
        normalizedStr = normalizedStr.replace(/[^a-z0-9\s]/g, '');
        normalizedStr = normalizedStr.replace(/\s+/g, ' ').trim();
        return normalizedStr;
    }

    // Fungsi untuk mendapatkan parameter dari URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Fungsi untuk melakukan pencarian (baik dari input manual maupun dari URL)
    function performExternalSearchAndDisplay(searchTerm) {
        if (!searchTerm || searchTerm.length <= 2) {
            searchMarkers.clearLayers();
            return;
        }
        console.log("Melakukan pencarian eksternal untuk:", searchTerm);
        searchMarkers.clearLayers(); // Bersihkan marker pencarian lama

        const encodedSearchTerm = encodeURIComponent(searchTerm); // Gunakan searchTerm asli untuk encoding
        const bboxParts = JABODETABEK_BBOX.split(',');
        const orsBboxParams = `boundary.rect.min_lon=${bboxParts[0]}&boundary.rect.min_lat=${bboxParts[1]}&boundary.rect.max_lon=${bboxParts[2]}&boundary.rect.max_lat=${bboxParts[3]}`;
        const orsGeocodingUrl = `https://api.openrouteservice.org/geocode/search?api_key=${ORS_API_KEY}&text=${encodedSearchTerm}&${orsBboxParams}&boundary.country=IDN&size=1`; // size=1 untuk hasil terbaik

        console.log("URL Geocoding ORS:", orsGeocodingUrl);

        fetch(orsGeocodingUrl)
            .then(response => {
                if (!response.ok) {
                    if (response.status === 403) {
                         console.error('Error 403: Akses ditolak ke OpenRouteService Geocoding. Periksa API Key atau izin.');
                         alert('Gagal mengambil data geocoding: Akses ditolak. Periksa API Key Anda.');
                    } else if (response.status === 429) {
                        console.error('Error 429: Too many requests to OpenRouteService Geocoding.');
                        alert('Terlalu banyak permintaan ke OpenRouteService. Mohon tunggu dan coba lagi.');
                    } else {
                        console.error(`Geocoding HTTP error! status: ${response.status}`);
                        alert('Terjadi kesalahan saat mengambil data geocoding dari OpenRouteService.');
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Response Geocoding ORS:", data);
                if (data.features && data.features.length > 0) {
                    const result = data.features[0];
                    const lon = result.geometry.coordinates[0];
                    const lat = result.geometry.coordinates[1];
                    const displayName = result.properties.label || searchTerm; // Gunakan label dari API atau searchTerm asli

                    const marker = L.marker([lat, lon])
                        .bindPopup(`<b>${displayName}</b><br><i>(Hasil Pencarian Eksternal)</i>`)
                        .openPopup();
                    searchMarkers.addLayer(marker);
                    map.setView([lat, lon], 16);

                    // Otomatis set tujuan jika ini hasil dari search_query URL
                    if (destinationSelect && searchTerm === getUrlParameter('search_query')) { // Cek apakah ini dari URL
                        const geocodedOption = document.createElement('option');
                        geocodedOption.value = `coords:${lat},${lon}`; // Simpan koordinat langsung
                        geocodedOption.textContent = `Dicari: ${displayName.substring(0, 30)}...`;
                        geocodedOption.selected = true;
                        destinationSelect.appendChild(geocodedOption);
                        console.log("Tujuan otomatis diatur ke hasil geocoding:", displayName);
                         if (userMarker && originSelect) { // Jika lokasi user sudah ada
                            originSelect.value = 'user_location';
                        }
                    }

                } else {
                    console.log("Tidak ada lokasi yang cocok dari OpenRouteService untuk:", searchTerm);
                    alert("Alamat '" + searchTerm + "' tidak ditemukan melalui pencarian eksternal.");
                }
            })
            .catch(error => {
                console.error('Error fetching geocoding data from OpenRouteService:', error);
                // Alert hanya jika belum ditangani oleh blok .then() sebelumnya
                if (error.message && !error.message.includes('HTTP error!') && !error.message.includes('429') && !error.message.includes('403')) {
                    alert('Terjadi kesalahan pada koneksi saat geocoding.');
                }
            });
    }


    // Memuat POI Kampus dan memproses search_query dari URL
    fetch('routes.php')
        .then(response => response.json())
        .then(data => {
            locations = data; // Simpan POI kampus

            const userLocationOption = document.createElement('option');
            userLocationOption.value = 'user_location';
            userLocationOption.textContent = 'Lokasi Anda';
            if(originSelect) originSelect.appendChild(userLocationOption);
            if(destinationSelect) destinationSelect.appendChild(userLocationOption.cloneNode(true));

            locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = location.name;
                if(originSelect) originSelect.appendChild(option);
                if(destinationSelect) destinationSelect.appendChild(option.cloneNode(true));
                // Tidak perlu tambah marker POI di sini lagi jika searchMarkers akan menanganinya
                // atau jika Anda ingin POI selalu tampil, biarkan:
                // L.marker([location.latitude, location.longitude]).addTo(map).bindPopup(location.name);
            });

            // Ambil search_query dari URL setelah POI dimuat (untuk prioritas jika nama sama)
            const searchQueryFromUrl = getUrlParameter('search_query');
            if (searchQueryFromUrl) {
                console.log("Menerima search_query dari URL:", searchQueryFromUrl);
                if (searchInput) searchInput.value = searchQueryFromUrl; // Isi ke field search
                
                // Coba cari dulu di POI lokal (kampus)
                const normalizedSearchFromUrl = normalizeString(searchQueryFromUrl);
                const foundInLocalPOIs = locations.find(loc => normalizeString(loc.name).includes(normalizedSearchFromUrl));

                if (foundInLocalPOIs) {
                    console.log("Alamat dari URL ditemukan di POI Lokal:", foundInLocalPOIs.name);
                    searchMarkers.clearLayers();
                    const marker = L.marker([foundInLocalPOIs.latitude, foundInLocalPOIs.longitude])
                                     .bindPopup(foundInLocalPOIs.name).openPopup();
                    searchMarkers.addLayer(marker);
                    map.setView([foundInLocalPOIs.latitude, foundInLocalPOIs.longitude], 16);
                    if(destinationSelect) destinationSelect.value = foundInLocalPOIs.id; // Otomatis pilih di dropdown
                     if (userMarker && originSelect) {
                        originSelect.value = 'user_location';
                    }
                } else {
                    // Jika tidak ada di POI lokal, baru lakukan geocoding eksternal
                    performExternalSearchAndDisplay(searchQueryFromUrl);
                }
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat memuat data lokasi POI kampus.');
            console.error("Fetch error untuk routes.php:", error);
        });

    // Event listener untuk input search manual
    if (searchInput) {
        searchInput.addEventListener('input', function (event) { // 'input' lebih responsif drpd 'keyup' untuk live search
            const rawSearchTerm = event.target.value;
            // Kita bisa panggil performExternalSearchAndDisplay di sini juga jika mau,
            // atau tetap dengan logika pencarian lokal yang ada di versi Anda:
            
            // Logika pencarian lokal Anda sebelumnya:
            const normalizedSearchTerm = normalizeString(rawSearchTerm);
            searchMarkers.clearLayers();
            if (normalizedSearchTerm.length > 2) {
                const foundLocations = locations.filter(location =>
                    normalizeString(location.name).includes(normalizedSearchTerm)
                );
                if (foundLocations.length > 0) {
                    foundLocations.forEach(location => {
                        const marker = L.marker([location.latitude, location.longitude])
                            .bindPopup(location.name)
                            .openPopup();
                        searchMarkers.addLayer(marker);
                    });
                    if (searchMarkers.getLayers().length > 0) {
                         map.fitBounds(searchMarkers.getBounds(), { padding: [50, 50] });
                    }
                } else {
                    // Jika Anda ingin otomatis geocode saat input manual tidak menemukan di POI:
                    // performExternalSearchAndDisplay(rawSearchTerm); 
                    // Untuk sekarang, biarkan seperti ini (hanya cari di POI lokal saat input manual)
                }
            }
        });
    }
    
    // Mendapatkan lokasi pengguna secara live menggunakan geolocation
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLon = position.coords.longitude;

            if (!userMarker) {
                userMarker = L.marker([userLat, userLon]).addTo(map);
                userMarker.bindPopup("Lokasi Anda Sekarang").openPopup();
            } else {
                userMarker.setLatLng([userLat, userLon]);
            }
            // map.setView([userLat, userLon], 16); // Hati-hati, ini bisa override zoom dari hasil search

            // (Komentari fetch ke save_location.php jika belum diperbaiki SQL Injectionnya)
            // fetch('save_location.php', { /* ... */ });
        }, function(error) {
            console.error('Error mendapatkan lokasi:', error);
        }, { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 });
    } else {
        alert('Browser Anda tidak mendukung geolocation.');
    }

    // Event listener untuk form rute
    const routeForm = document.getElementById('routeForm');
    if (routeForm) {
        routeForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const originValue = originSelect.value;
            const destinationValue = destinationSelect.value;
            const selectedTravelMode = document.getElementById('travelMode').value;

            if (!originValue || !destinationValue) {
                alert('Pilih lokasi asal dan tujuan terlebih dahulu.');
                return;
            }

            let originCoords, destinationCoords;
            let originName = "Asal", destinationName = "Tujuan";

            // Mendapatkan koordinat Asal
            if (originValue === 'user_location') {
                if (!userMarker) { alert("Lokasi Anda belum tersedia."); return; }
                originCoords = { latitude: userMarker.getLatLng().lat, longitude: userMarker.getLatLng().lng };
                originName = "Lokasi Anda";
            } else {
                const foundOrigin = locations.find(loc => loc.id == originValue);
                if (foundOrigin) {
                    originCoords = { latitude: parseFloat(foundOrigin.latitude), longitude: parseFloat(foundOrigin.longitude) };
                    originName = foundOrigin.name;
                } else { alert('Lokasi asal dari daftar POI tidak valid.'); return; }
            }

            // Mendapatkan koordinat Tujuan
            if (destinationValue === 'user_location') {
                if (!userMarker) { alert("Lokasi Anda belum tersedia."); return; }
                destinationCoords = { latitude: userMarker.getLatLng().lat, longitude: userMarker.getLatLng().lng };
                destinationName = "Lokasi Anda";
            } else if (destinationValue.startsWith('coords:')) { // Untuk tujuan hasil geocoding
                const parts = destinationValue.replace('coords:', '').split(',');
                destinationCoords = { latitude: parseFloat(parts[0]), longitude: parseFloat(parts[1]) };
                // Mencari nama yang sesuai dari opsi yang dipilih (karena kita simpan nama di textContent)
                for(let i=0; i < destinationSelect.options.length; i++){
                    if(destinationSelect.options[i].value === destinationValue){
                        destinationName = destinationSelect.options[i].textContent.replace('Dicari: ','').replace('...','');
                        break;
                    }
                }
                if(destinationName === "Tujuan") destinationName = "Alamat Eksternal"; // fallback
            } else {
                const foundDestination = locations.find(loc => loc.id == destinationValue);
                if (foundDestination) {
                    destinationCoords = { latitude: parseFloat(foundDestination.latitude), longitude: parseFloat(foundDestination.longitude) };
                    destinationName = foundDestination.name;
                } else { alert('Lokasi tujuan dari daftar POI tidak valid.'); return; }
            }
            
            // Validasi koordinat sebelum fetch rute
            if (!originCoords || !destinationCoords || 
                isNaN(originCoords.latitude) || isNaN(originCoords.longitude) ||
                isNaN(destinationCoords.latitude) || isNaN(destinationCoords.longitude)) {
                alert('Koordinat asal atau tujuan tidak valid. Tidak bisa menghitung rute.');
                return;
            }


            // Hapus marker dan rute lama (kecuali userMarker dan searchMarkers group)
             map.eachLayer(function(layer) {
                if (layer !== userMarker && !(searchMarkers.hasLayer(layer)) && (layer instanceof L.Marker || layer instanceof L.Polyline) ) {
                    map.removeLayer(layer);
                }
            });
            if (currentRoutePolyline) { // Hapus polyline rute sebelumnya jika ada
                map.removeLayer(currentRoutePolyline);
                currentRoutePolyline = null;
            }
            // searchMarkers.clearLayers(); // Bersihkan marker hasil search sebelumnya jika sudah tidak relevan


            // Tambah marker asal dan tujuan baru untuk rute ini (jika bukan userMarker)
            // dan pastikan tidak duplikat dengan yang mungkin sudah ada dari searchMarkers
            let tempOriginMarker, tempDestinationMarker;
            if (originValue !== 'user_location') {
                 tempOriginMarker = L.marker([originCoords.latitude, originCoords.longitude]).addTo(map).bindPopup("Asal: " + originName).openPopup();
            } else if (userMarker) {
                 userMarker.bindPopup("Asal: " + originName).openPopup();
            }

            if (destinationValue !== 'user_location') {
                 tempDestinationMarker = L.marker([destinationCoords.latitude, destinationCoords.longitude]).addTo(map).bindPopup("Tujuan: " + destinationName).openPopup();
            } else if (userMarker) {
                 userMarker.bindPopup("Tujuan: " + destinationName).openPopup(); // Jika tujuan adalah lokasi user
            }


            const url = `https://api.openrouteservice.org/v2/directions/${selectedTravelMode}?api_key=${ORS_API_KEY}&start=${originCoords.longitude},${originCoords.latitude}&end=${destinationCoords.longitude},${destinationCoords.latitude}`;
            console.log("Mencoba mengambil rute dengan URL:", url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(`API Rute error (${response.status}): ${errData.error?.message || JSON.stringify(errData) }`);
                        }).catch(() => {
                             throw new Error(`API Rute error (${response.status}): ${response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Data Rute ORS:", data);
                    document.getElementById('instructions').innerHTML = ''; 
                    document.getElementById('timeEstimates').innerHTML = '';

                    if (data.features && data.features.length > 0) {
                        const route = data.features[0].geometry.coordinates;
                        const instructions = data.features[0].properties.segments[0].steps;
                        const durationInSeconds = data.features[0].properties.summary?.duration || data.features[0].properties.segments[0].duration;

                        currentRoutePolyline = L.polyline(route.map(coord => [coord[1], coord[0]]), {color: 'blue'}).addTo(map);
                        map.fitBounds(currentRoutePolyline.getBounds(), {padding: [20,20]});

                        let instructionsList = '<ul>';
                        instructions.forEach(step => {
                            instructionsList += `<li>${step.instruction} (jarak: ${step.distance.toFixed(0)} m)</li>`;
                        });
                        instructionsList += '</ul>';
                        document.getElementById('instructions').innerHTML = instructionsList;

                        const durationInMinutes = durationInSeconds / 60;
                        document.getElementById('timeEstimates').innerHTML = `Estimasi Waktu (${document.getElementById('travelMode').options[document.getElementById('travelMode').selectedIndex].text}): <strong>${durationInMinutes.toFixed(0)} menit</strong>`;
                    } else {
                        alert('Tidak ada rute ditemukan antara lokasi tersebut.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching route:', error);
                    alert('Terjadi kesalahan saat mengambil rute: ' + error.message);
                });
        });
    }
});