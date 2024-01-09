$(document).ready(function () {
    const countriesApiUrl = 'https://restcountries.com/v3.1/all';

    // Fetch the data and populate the select element
    $.get(countriesApiUrl, function (data) {
        if (Array.isArray(data)) {
            // Clear existing options in elements with class "country-select"
            $('.country-select').empty();

            // Add the "Please Select Country" option as the first option
            $('.country-select').append($('<option>', {
                value: 'United Kingdom',
                text: 'United Kingdom'
            }));

            // Create and append the options for the countries
            data.forEach(function (country) {
                const commonName = country.name.common;
                if (commonName) {
                    $('.country-select').append($('<option>', {
                        value: commonName,
                        text: commonName
                    }));
                }
            });
            $('.country-select').val('United Kingdom');
        } else {
            console.error('Failed to fetch country data');
        }
    }).fail(function () {
        console.error('Failed to make the API request');
    });




});