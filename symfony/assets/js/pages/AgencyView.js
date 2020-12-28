import React from 'react';
import AgencyBranch from "../components/AgencyBranch";
import {Container} from "reactstrap";
import LoadingSpinner from "../components/LoadingSpinner";

class AgencyView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agencySlug: this.props.match.params.slug,
            agencyLoading: false,
            agencyLoaded: false,
            agency: {}
        };
    }

    componentDidMount() {
        this.fetchAgencyData();
    }

    render() {
        return (
            <Container>
                {this.state.loading &&
                    <LoadingSpinner />
                }
                {!this.state.agencyLoading && this.state.agencyLoaded &&
                    <div>
                        <div className="col-md-12 page-title">
                            <h1>{this.state.agency.name}</h1>
                        </div>
                        <div className="col-md-12">
                            <div className="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                                {this.state.agencyLoaded && this.state.agency.branches.map(
                                    ({ slug, name, telephone, email }) => (
                                        <AgencyBranch
                                            key={slug}
                                            slug={slug}
                                            name={name}
                                            telephone={telephone}
                                            email={email}
                                        >
                                        </AgencyBranch>
                                    )
                                )}
                            </div>
                        </div>
                    </div>
                }
            </Container>
        );
    }

    fetchAgencyData() {
        this.setState({agencyLoading: true});
        fetch(
            '/api/agency/' + this.state.agencySlug,
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(response => response.json())
            .then(agency => {
                this.setState({
                    agency,
                    agencyLoading: false,
                    agencyLoaded: true
                });
            });
    }
}

export default AgencyView;
