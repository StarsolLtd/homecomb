import React from 'react';
import RatedAgency from "./RatedAgency";

class RatedAgencies extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            heading: this.props.heading,
            agencyReviewsSummary: this.props.agencyReviewsSummary
        };
    }

    render() {
        return (
            <div className="top-rated-agencies">
                <h5 className="mb-3">{this.state.heading}</h5>

                {this.state.agencyReviewsSummary.agencyReviewSummaries.map(
                    ({ agencySlug, agencyName, agencyLogoImageFilename, meanRating, ratedCount }) => (
                        <RatedAgency
                            key={agencySlug}
                            agencySlug={agencySlug}
                            agencyName={agencyName}
                            agencyLogoImageFilename={agencyLogoImageFilename}
                            meanRating={meanRating}
                            ratedCount={ratedCount}
                        >
                        </RatedAgency>
                    )
                )}

                {this.state.agencyReviewsSummary.agencyReviewSummaries.length === 0 &&
                    <p>
                        There are currently no rated agencies here.
                    </p>
                }
            </div>
        );
    }
}

export default RatedAgencies;