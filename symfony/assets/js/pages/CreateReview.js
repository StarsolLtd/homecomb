import React, {Fragment} from 'react';
import {Container, Col, Row} from 'reactstrap';
import ReviewTenancyForm from "../components/ReviewTenancyForm";

class CreateReview extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            code: this.props.match.params.code,
            loading: false,
            loaded: false,
            agency: null,
            branch: null,
            property: null,
            reviewerFirstName: '',
            reviewerLastName: '',
            reviewerEmail: '',
        };
    }

    componentDidMount() {
        this.fetchData();
    }

    render() {
        return (
            <Container>
                {this.state.loading &&
                    <div>
                        <div className="spinner-border" role="status">
                            <span className="sr-only">Loading...</span>
                        </div>
                    </div>
                }
                {!this.state.loading && this.state.loaded &&
                    <Fragment>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>Hello {this.state.reviewerFirstName}!</h1>
                            </Col>
                        </Row>

                        <Row>
                            <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                                <p>
                                    {this.state.agency.name} has asked that you review your tenancy at {this.state.property.addressLine1}
                                </p>

                                <p>
                                    Please complete the form below to submit your review:
                                </p>

                                <hr />

                                <ReviewTenancyForm
                                    code={this.state.code}
                                    fixedBranch={true}
                                    branch={this.state.branch}
                                    agency={this.state.agency}
                                    propertySlug={this.state.property.slug}
                                    reviewerEmail={this.state.reviewerEmail}
                                    reviewerName={this.state.reviewerFirstName + ' ' + this.state.reviewerLastName}
                                />
                            </Col>
                        </Row>
                    </Fragment>
                }
            </Container>
        );
    }

    fetchData() {
        this.setState({loading: true});
        fetch(
            '/api/rs/' + this.state.code,
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(response => response.json())
            .then(data => {
                this.setState({
                    agency: data.agency,
                    branch: data.branch,
                    property: data.property,
                    reviewerFirstName: data.reviewerFirstName,
                    reviewerLastName: data.reviewerLastName,
                    reviewerEmail: data.reviewerEmail,
                    loading: false,
                    loaded: true
                });
            });
    }
}

export default CreateReview;