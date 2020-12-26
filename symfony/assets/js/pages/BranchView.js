import React, {Fragment} from 'react';
import ReactDOM from 'react-dom';
import {Col, Row} from 'reactstrap';

class BranchView extends React.Component {
    constructor() {
        super();
        this.state = {
            branchSlug: window.branchSlug,
            loading: false,
            loaded: false,
            agency: {},
            branch: {},
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
                                <h1>{this.state.branch.name}</h1>
                            </Col>
                        </Row>
                        <div className="bg-white rounded shadow-sm p-4 mb-4">
                            <Row>
                                <Col xs="12" md="8">
                                    <h5 className="mb-1">Reviews from tenants</h5>

                                </Col>

                                <Col md="4" className="d-sm-none d-md-block branch-agency">
                                    {this.state.agency.logoImageFilename &&
                                        <img src={'/images/images/' + this.state.agency.logoImageFilename} className="agency-logo" />
                                    }

                                    <h5 className="mb-1">{this.state.branch.name} contact details</h5>

                                    <p>
                                        {this.state.branch.telephone &&
                                            <span>Telephone: {this.state.branch.telephone}</span>
                                        }
                                        {this.state.branch.email &&
                                            <span>Email: <a href={'mailto:' + this.state.branch.email}>{this.state.branch.email}</a></span>
                                        }
                                    </p>
                                </Col>
                            </Row>
                        </div>
                    </div>
                }
            </Fragment>
        );
    }

    fetchData() {
        this.setState({loading: true});
        fetch(
            '/api/branch/' + window.branchSlug,
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
                    reviews: data.reviews,
                    loading: false,
                    loaded: true
                });
            });
    }
}

ReactDOM.render(<BranchView />, document.getElementById('branch-view-root'));