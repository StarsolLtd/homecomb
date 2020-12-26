import React, {Fragment} from 'react';
import ReactDOM from 'react-dom';
import {Col, Row} from 'reactstrap';
import Review from "../components/Review";

class PropertyView extends React.Component {
    constructor() {
        super();
        this.state = {
            propertySlug: window.propertySlug,
            loading: false,
            loaded: false,
            addressLine1: '',
            postcode: '',
            reviews: []
        };
    }

    componentDidMount() {
        this.fetchData();
    }

    render() {
        return (
            <Fragment>
                {this.state.loading &&
                <div>
                    <div className="spinner-border" role="status">
                        <span className="sr-only">Loading branch...</span>
                    </div>
                </div>
                }
                {!this.state.loading && this.state.loaded &&
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
                                        <Fragment>
                                            <Review
                                                key={id}
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
                    </div>
                }
            </Fragment>
        );
    }

    fetchData() {
        this.setState({loading: true});
        fetch(
            '/api/property/' + this.state.propertySlug,
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
                    addressLine1: data.addressLine1,
                    postcode: data.postcode,
                    reviews: data.reviews,
                    loading: false,
                    loaded: true
                });
            });
    }
}

ReactDOM.render(<PropertyView />, document.getElementById('property-view-root'));