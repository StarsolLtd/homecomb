import React from 'react';
import ReactDOM from 'react-dom';

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
        this.fetchData();
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
                    <h1 className="display-5">{this.state.agency.name}</h1>
                }
            </div>
        );
    }

    fetchData() {
        this.fetchAgencyData();
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