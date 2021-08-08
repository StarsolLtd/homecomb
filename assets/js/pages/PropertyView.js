import React, {Fragment} from 'react';
import {Button, Container, Col, Row, Breadcrumb, BreadcrumbItem} from 'reactstrap';
import Review from "../components/Review";
import ReviewTenancyForm from "../components/ReviewTenancyForm";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";
import PropertyAutocomplete from "../components/PropertyAutocomplete";
import ReviewCompletedThankYou from "../content/ReviewCompletedThankYou";
import Map from "../components/Map";
import {Link} from "react-router-dom";

class PropertyView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            addressLine1: '',
            locality: null,
            postcode: '',
            city: null,
            tenancyReviews: [],
            reviewTenancyFormOpen: false,
            loaded: false,
            reviewCompletedThankYou: false,
            latitude: null,
            longitude: null,
        };
        this.openReviewTenancyForm = this.openReviewTenancyForm.bind(this);
        this.loadData = this.loadData.bind(this);
        this.reviewCompletedThankYou = this.reviewCompletedThankYou.bind(this);
    }

    openReviewTenancyForm() {
        this.setState({reviewTenancyFormOpen: true});
    }

    reviewCompletedThankYou() {
        this.setState({
            reviewCompletedThankYou: true,
            reviewTenancyFormOpen: false
        })
    }

    render() {
        return (
            <Container>
                {this.state.reviewCompletedThankYou &&
                    <ReviewCompletedThankYou />
                }
                <DataLoader
                    url={'/api/property/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                <div className="property-view">
                    <Row>
                        <Breadcrumb className="w-100">
                            <BreadcrumbItem><Link to="/">Home</Link></BreadcrumbItem>
                            {this.state.city &&
                                <BreadcrumbItem className="city">{this.state.city.name}</BreadcrumbItem>
                            }
                            {this.state.locality &&
                                <BreadcrumbItem className="locality">{this.state.locality}</BreadcrumbItem>
                            }
                            <BreadcrumbItem className="postcode">{this.state.postcode}</BreadcrumbItem>
                            <BreadcrumbItem className="active address-line-1">{this.state.addressLine1}</BreadcrumbItem>
                        </Breadcrumb>
                    </Row>
                    {this.state.latitude && this.state.longitude &&
                        <Row>
                            <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                                <Map
                                    className="property-map"
                                    addressLine1={this.state.addressLine1}
                                    latitude={this.state.latitude}
                                    longitude={this.state.longitude}
                                />
                            </Col>
                        </Row>
                    }
                    <Row>
                        <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                            <h5 className="mb-1">Reviews from tenants</h5>

                            {this.state.tenancyReviews.map(
                                ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                                    <Fragment key={id}>
                                        <Review
                                            {...this.props}
                                            key={id}
                                            id={id}
                                            author={author}
                                            start={start}
                                            end={end}
                                            title={title}
                                            content={content}
                                            property={property}
                                            branch={branch}
                                            agency={agency}
                                            stars={stars}
                                            createdAt={createdAt}
                                            comments={comments}
                                            positiveVotes={positiveVotes}
                                            showProperty={false}
                                        >
                                        </Review>
                                        <hr />
                                    </Fragment>
                                )
                            )}

                            {this.state.tenancyReviews.length === 0 &&
                            <Fragment>
                                <p className="mt-3">
                                    There are no tenant reviews yet for this property.
                                </p>
                                <hr />
                                <h5 className="mb-4">Search for another property address</h5>
                                <PropertyAutocomplete prependSearchIcon={true}/>
                            </Fragment>
                            }
                        </Col>
                    </Row>
                    <Row>
                        <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                            <h5 className="mb-4">Review your tenancy here</h5>

                            <p className="mb-2">
                                Are you a current or past tenant at {this.state.addressLine1}?
                                We'd love it if you could review your tenant experience!
                            </p>
                            <hr />
                            {!this.state.reviewTenancyFormOpen &&
                            <Button onClick={this.openReviewTenancyForm} color="primary">Yes! I want to write a review</Button>
                            }
                            {this.state.reviewTenancyFormOpen &&
                            <ReviewTenancyForm
                                fixedProperty={true}
                                propertySlug={this.props.match.params.slug}
                                completedThankYou={this.reviewCompletedThankYou}
                                {...this.props}
                            />
                            }
                        </Col>
                    </Row>
                </div>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            addressLine1: data.addressLine1,
            locality: data.locality,
            postcode: data.postcode,
            latitude: data.latitude,
            longitude: data.longitude,
            tenancyReviews: data.tenancyReviews,
            city: data.city,
            loaded: true,
        });

        document.title = data.addressLine1 + ' | ' + Constants.SITE_NAME;
    }
}

export default PropertyView;