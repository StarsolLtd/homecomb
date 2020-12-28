import React, {Fragment} from 'react';
import {Link} from "react-router-dom";

class RatedAgency extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            heading: this.props.heading,
            agencySlug: this.props.agencySlug,
            agencyName: this.props.agencyName,
            agencyLogoImageFilename: this.props.agencyLogoImageFilename,
            meanRating: this.props.meanRating,
            ratedCount: this.props.ratedCount
        };
    }

    render() {
        return (
            <div className="agency clearfix">
                <div className="float-left agency-logo-container">
                    {this.state.agencyLogoImageFilename &&
                        <Link to={'/agency/' + this.state.agencySlug}>
                            <img src={'/images/images/' + this.state.agencyLogoImageFilename}
                                 className="agency-logo float-left"
                                 alt={this.state.agencyName + ' Logo'}
                            />
                        </Link>
                    }
                </div>
                <div>
                    <p>
                        <a href={'/agency/' + this.state.agencySlug}>{this.state.agencyName}</a><br />
                        Average rating: { this.state.meanRating } stars from { this.state.ratedCount} review
                        {this.state.ratedCount > 1 &&
                            <Fragment>s</Fragment>
                        }
                    </p>
                </div>
            </div>
        );
    }
}

export default RatedAgency;