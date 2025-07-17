# CA assignment dev notes

Below are the quotes from the task specification, followed by design decisions and/or considerations.<br/><br/>

### Some important decisions
This service will ...
> ... regularly fetch reviews from the provider

**Messenger component + scheduler**

As we are talking about the data synchronization process here, which is optimal to run in the background, asynchronously, the process is implemented with the help of the Messenger component, which provides asynchronous, decoupled processing.
It also allows us to synchronize data from multiple external sources.
Sending messages that initiate synchronization processes can be scheduled in a number of ways ... 
<br />
<br />

> ... normalize the data <br/>

**DTOs + Serializer**

Deserialize external JSON → DTO using the power of Symfony serializer.

### Data consistency
> Handles reviews disappearing from the provider (keep their last known data; never delete)

The implementation meets this requirement, except that the records in the database remain as they are, not additionally marked/archived (it seemed to me that this operation was outside the scope of the task).

> Only keeps reviews where status is published (filter out drafts/spam)

This requirement is met by storing incoming data (reviews that do not already exist in the database) only if their status has been *published*. If, on the other hand, the data exists in the database,these entries are updated, thus storing the last known state at that moment (request from the quote below).

> The system should update the existing review to reflect the latest known version

Existing data is updated only if upcoming data of the same identification is more recent than it.

### API endpoint specs and considerations
> The endpoint should be fast in hundreds of ms magnitude order, regardless of the state of other external services. For instance, if the external provider service is down, our search endpoint should still work as usual.

Taking into account the defined filters and the corresponding db query, the migration doctrine also contains two additional composite indexes to improve performance in the case of a large number of rows in the tables.
As noted in OpenAPI specification for the endpoint, the results are fetched only from a local database.

### The extra mile
As mentioned above, some optimization is done on the database. That could be of course improved by introducing a caching layer on the output (internal API endpoint) as well on the data consuming side. 
I think that the design which introduces the Messenger component and its workers opens a good possibility for horizontal scaling, and provides good performance by default. 
The solution contains pluggable ProviderRegistry based on the 'tagged services' Symfony feature, and makes adding new data providers to the equasion pretty straight forward. It is in place in code.

**Important note on the topic** The provider directory only contains file based provider (there for development reasons), since the real world external provider implementation is considered abstract. This provider is auto loaded to the provider registry, and can be initiated by the DemoReviewController directly.  

### Missing stuff due to time limitation (that I wish I had done)
- Symfony Scheduler compoment for scheduling message sending (scheduling is done only as a small deamon in the Make file for simplicity reasons)
- Functional tests
- API authentication ?
