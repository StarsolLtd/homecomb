import React, {Fragment} from 'react';
import ReactDOM from 'react-dom';
import {Col, Row} from 'reactstrap';
import Review from "../components/Review";

class LocaleView extends React.Component {
    constructor() {
        super();
        this.state = {
            localeSlug: window.localeSlug,
            loading: false,
            loaded: false,
            name: '',
            reviews: [],
            agencyReviewsSummary: null
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
                        <span className="sr-only">Loading...</span>
                    </div>
                </div>
                }
                {!this.state.loading && this.state.loaded &&
                    <div>
                        <Row>
                            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                                <h5 className="mb-1">Reviews from tenants</h5>

                                {this.state.reviews.map(
                                    ({ id, author, title, content, property, branch, agency, stars, createdAt }) => (
                                        <Fragment>
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
            '/api/l/' + this.state.localeSlug,
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
                    name: data.name,
                    reviews: data.reviews,
                    agencyReviewsSummary: data.agencyReviewsSummary,
                    loading: false,
                    loaded: true
                });
            });
    }
}

ReactDOM.render(<LocaleView />, document.getElementById('locale-view-root'));