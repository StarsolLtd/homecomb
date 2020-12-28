import React from 'react';
import Stars from "./Stars";

class ReviewStars extends React.Component {
    render() {
        return (
            <div className="review-stars">
                {this.props.overall &&
                    <Stars label="Overall" score={this.props.overall}/>
                }
                {this.props.agency &&
                    <Stars label="Agency" score={this.props.agency}/>
                }
                {this.props.landlord &&
                    <Stars label="Landlord" score={this.props.landlord}/>
                }
                {this.props.property &&
                    <Stars label="Property" score={this.props.property}/>
                }
            </div>
        );
    }
}

export default ReviewStars;