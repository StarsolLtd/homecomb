import React, {Fragment} from 'react';
import {Breadcrumb, BreadcrumbItem, Col, Container, Row} from 'reactstrap';
import Review from "../components/Review";
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";
import {Link} from "react-router-dom";

class BranchView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            branchSlug: this.props.match.params.slug,
            agency: {},
            branch: {},
            tenancyReviews: [],
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
                    <Fragment>
                        <Row>
                            <Breadcrumb className="w-100">
                                <BreadcrumbItem><Link to="/">{Constants.SITE_NAME}</Link></BreadcrumbItem>
                                {this.state.agency &&
                                <BreadcrumbItem className="agency-name"><Link to={'/agency/' + this.state.agency.slug}>{this.state.agency.name}</Link></BreadcrumbItem>
                                }
                                <BreadcrumbItem className="active branch-name">{this.state.branch.name}</BreadcrumbItem>
                            </Breadcrumb>
                        </Row>
                        <Row className="bg-white rounded shadow-sm p-4 mb-4">
                            <Col xs="12" md="8">
                                <h5 className="mb-1">Reviews from tenants</h5>

                                {this.state.tenancyReviews.map(
                                    ({ id, author, title, content, start, end, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
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
                    </Fragment>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            agency: data.agency,
            branch: data.branch,
            tenancyReviews: data.tenancyReviews,
            loaded: true,
        });

        document.title = this.state.branch.name + ' Branch Reviews | ' + Constants.SITE_NAME;
    }
}

export default BranchView;
