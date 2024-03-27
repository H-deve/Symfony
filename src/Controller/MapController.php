<?php

namespace App\Controller;
use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

class MapController extends AbstractController
{
    /**
     * @Route("/map", name="map")
     */
    
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function map(): Response
    {
        // Fetch address data from the database
        $addressRepository = $this->doctrine->getRepository(Address::class);
        $addresses = $addressRepository->findAll();

        // Convert data to GeoJSON format
       // Initialize an empty array to store GeoJSON features
       $geojson = [
        'type' => 'FeatureCollection',
        'features' => []
    ];

    // Loop through each address
    foreach ($addresses as $address) {
        // Get address details
        $name = $address->getName();
        $description = $address->getDescription();
        $city = $address->getCity();
        $street = $address->getstreet();
        $country = $address->getCountry();
        $code_postal=$address->getPostalCode();
        $latitude = $address->getLatitude();
        $longitude = $address->getLongitude();
        $website = $address->getWebsite();

        // Create a GeoJSON feature for the address
        $feature = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$longitude, $latitude],
            ],
            'properties' => [
                'title' => $name,
                'description' => $description, 
                'city' => $city,
                'street'=>$street,
                'country'=>$country,
                'code_postal'=>$code_postal,
                'website'=>$website,
            ],
        ];

        // Add the GeoJSON feature to the array
        $geojson['features'][] = $feature;
    }

    // Pass the GeoJSON data to the Twig template
    return $this->render('map/index.html.twig', [
        'geojson' => json_encode($geojson),
    ]);
}
}
