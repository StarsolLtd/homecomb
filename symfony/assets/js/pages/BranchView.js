import React, {Fragment} from 'react';
import {Col, Container, Row} from 'reactstrap';
import Review from "../components/Review";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";

class BranchView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            branchSlug: this.props.match.params.slug,
            agency: {},
            branch: {},
            reviews: [],
            loaded: false,
        };

        this.loadData = this.loadData.bind(this);
    }

    render() {
        return (
            <Container>
                <DataLoader
                    url={'/api/branch/' + this.state.branchSlug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>
                                    {this.state.branch.name}
                                    {this.state.agency &&
                                        <span className="agency-name"> - {this.state.agency.name}</span>
                                    }
                                </h1>
                            </Col>
                        </Row>
                        <div className="bg-white rounded shadow-sm p-4 mb-4">
                            <Row>
                                <Col xs="12" md="8">
                                    <h5 className="mb-1">Reviews from tenants</h5>

                                    {this.state.reviews.map(
                                        ({ id, author, title, content, property, branch, agency, stars, createdAt }) => (
                                            <Fragment key={id}>
                                                <Review
                                                    {...this.props}
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
                                                    showBranch={false}
                                                >
                                                </Review>
                                                <hr />
                                            </Fragment>
                                        )
                                    )}

                                </Col>

                                <Col md="4" className="d-sm-none d-md-block branch-agency">
                                    {this.state.agency.logoImageFilename &&
                                        <img src={'/images/images/' + this.state.agency.logoImageFilename} className="agency-logo" />
                                    }

                                    <h5 className="mb-1">{this.state.branch.name} contact details</h5>

                                    <p>
                                        {this.state.branch.telephone &&
                                            <span>Telephone: {this.state.branch.telephone}<br /></span>
                                        }
                                        {this.state.branch.email &&
                                            <span>Email: <a href={'mailto:' + this.state.branch.email}>{this.state.branch.email}</a><br /></span>
                                        }
                                    </p>
                                </Col>
                            </Row>
                        </div>
                    </div>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            agency: data.agency,
            branch: data.branch,
            reviews: data.reviews,
            loaded: true,
        });

        document.title = this.state.branch.name + ' Branch Reviews | ' + Constants.SITE_NAME;
    }
}

export default BranchView;
