import React from 'react';
import ReactDOM from 'react-dom';
import AgencyBranch from "../components/AgencyBranch";

class AgencyView extends React.Component {
    constructor() {
        super();
        this.state = {
            agencySlug: window.agencySlug,
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
            <div>
                {this.state.agencyLoading &&
                <div>
                    <div className="spinner-border" role="status">
                        <span className="sr-only">Loading Agency...</span>
                    </div>
                </div>
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
            </div>
        );
    }

    fetchAgencyData() {
        this.setState({agencyLoading: true});
        fetch(
            '/api/agency/' + window.agencySlug,
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

ReactDOM.render(<AgencyView />, document.getElementById('agency-view-root'));