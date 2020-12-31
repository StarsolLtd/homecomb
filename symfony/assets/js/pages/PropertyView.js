import React, {Fragment} from 'react';
import {Button, Container, Col, Row} from 'reactstrap';
import Review from "../components/Review";
import ReviewTenancyForm from "../components/ReviewTenancyForm";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";

class PropertyView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            addressLine1: '',
            postcode: '',
            reviews: [],
            reviewTenancyFormOpen: false,
            loaded: false,
        };

        this.openReviewTenancyForm = this.openReviewTenancyForm.bind(this);
        this.loadData = this.loadData.bind(this);
    }

    openReviewTenancyForm() {
        this.setState({reviewTenancyFormOpen: true});
    }

    render() {
        return (
            <Container>
                <DataLoader
                    url={'/api/property/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>{this.state.addressLine1}, {this.state.postcode}</h1>
                            </Col>
                        </Row>
                        <Row>
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h5 className="mb-1">Reviews from tenants</h5>

                                {this.state.reviews.map(
                                    ({ id, author, title, content, property, branch, agency, stars, createdAt }) => (
                                        <Fragment key={id}>
                                            <Review
                                                key={id}
                                                id={id}
                                                author={author}
                                                title={title}
                                                content={content}
                                                property={property}
                                                branch={branch}
                                                agency={agency}
                                                stars={stars}
                                                createdAt={createdAt}
                                                showProperty={false}
                                            >
                                            </Review>
                                            <hr />
                                        </Fragment>
                                    )
                                )}
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
                                    <ReviewTenancyForm propertySlug={this.props.match.params.slug} />
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
            postcode: data.postcode,
            reviews: data.reviews,
            loaded: true,
        });

        document.title = data.addressLine1 + ' | ' + Constants.SITE_NAME;
    }
}

export default PropertyView;