export function getAllAddresses(address) {
    return fetch('https://api-adresse.data.gouv.fr/search/?q=' + address.split(' ').join('+') + '&limit=10').then(function(res) {
        return res.json().then(function(json) {
            const addresses = [];

            // TODO changer la récupération des données sur les adresses
            json.features.forEach(function(address) {
                addresses.push({
                    label: address.properties.label,
                    street: address.properties.name,
                    postcode: address.properties.postcode,
                    city: address.properties.city,
                    department: "",
                    region: "",
                    country: "FRANCE",
                    latitude: address.geometry.coordinates[1],
                    longitude: address.geometry.coordinates[0]
                });
            });
            return addresses;
        });
    });
}