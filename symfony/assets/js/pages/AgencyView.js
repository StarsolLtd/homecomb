import React from 'react';
import AgencyBranch from "../components/AgencyBranch";
import {Container} from "reactstrap";
import LoadingInfo from "../components/LoadingInfo";

class AgencyView extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            agencySlug: this.props.match.params.slug,
            agency: {},
            loadingInfo: {
                loaded: false,
                loading: false,
                loadingError: false,
                loadingErrorCode: null,
            },
        };
    }

    componentDidMount() {
        this.fetchAgencyData();
    }

    render() {
        return (
            <Container>
                <LoadingInfo
                    info={this.state.loadingInfo}
                />
                {!this.state.loadingInfo.loading && this.state.loadingInfo.loaded &&
                    <div>
                        <div className="col-md-12 page-title">
                            <h1>{this.state.agency.name}</h1>
                        </div>
                        <div className="col-md-12">
                            <div className="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                                {this.state.agency.branches.map(
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
        this.setState({loadingInfo: {loading: true}})
        fetch(
            '/api/agency/' + this.state.agencySlug,
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(
                response => {
                    this.setState({
                        loadingInfo: {loading: false},
                    })
                    if (!response.ok) {
                        this.setState({
                            loadingInfo: {
                                loadingError: true,
                                loadingErrorCode: response.status,
                            }
                        })
                        return Promise.reject('Error: ' + response.status)
                    }
                    return response.json()
                }
            )
            .then(agency => {
                this.setState({
                    agency,
                    loadingInfo: {
                        loading: false,
                        loaded: true
                    }
                });
            });
    }
}

export default AgencyView;
