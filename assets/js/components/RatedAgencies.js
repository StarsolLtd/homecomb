import React from 'react'
import RatedAgency from './RatedAgency'

const RatedAgencies = (props) => {
  return (
    <div className="top-rated-agencies">
      <h5 className="mb-3">{props.heading}</h5>

      {props.agencyReviewsSummary.agencyReviewSummaries.map(
        ({ agencySlug, agencyName, agencyLogoImageFilename, meanRating, ratedCount }) => (
          <RatedAgency
            key={agencySlug}
            agencySlug={agencySlug}
            agencyName={agencyName}
            agencyLogoImageFilename={agencyLogoImageFilename}
            meanRating={meanRating}
            ratedCount={ratedCount}
          />
        )
      )}

      {props.agencyReviewsSummary.agencyReviewSummaries.length === 0 &&
        <p>
          There are currently no rated agencies here.
        </p>
      }
    </div>
  )
}

export default RatedAgencies
