# Trouble Flight OTA PHP Example

This is a quick example of how to integrate our API into your website.

## Table of Contents

- [1. New Sale](#new-sale)
- [2. Example Endpoint For WebHooks](#example-endpoint-for-webhooks)
- [3. Landing](#landing)
- [Contact](#contact)

## New Sale

This example demonstrates how to call the Trouble Flight API to communicate a new booking. Trouble Flight will verify the flights, and if there is a disruption, the WebHook will be triggered.

Please note that the WebHook will only be triggered if the flight is disrupted.

To set up the WebHook, please email [claudio.mulas@troubleflight.com](mailto:claudio.mulas@troubleflight.com) with the URL of the WebHook and the method (POST-JSON/POST-BODY/GET).

## Example Endpoint For WebHooks

This example shows an endpoint where our system can trigger the WebHook if a booking flight is disrupted.

At this step, an email should be sent to the customer informing them that they are eligible for compensation.

Information such as the amount of compensation, regulations, and flight details will be included in every request.

## Landing

The email sent in the previous step will contain a link where the customer can choose whether to share their information with Trouble Flight. This step is necessary to comply with GDPR.

If the customer agrees to share their information, they will be redirected to a page with all the information pre-filled.

If the customer does not agree, they will be redirected to a standard affiliation link.

## Contact

For any technical questions, please email [claudio.mulas@troubleflight.com](mailto:claudio.mulas@troubleflight.com). For claim-related questions, email  [contact@troubleflight.com](mailto:contact@troublelfight.com).

[Trouble Flight](https://www.troubleflight.com/)
© 2020 - 2024. All right reserved. Doorify Tech S.R.L. The services available through the site are offered by Doorify Tech or its Partners.