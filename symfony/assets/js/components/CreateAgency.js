import React from 'react';
import ReactDOM from 'react-dom';

class CreateAgency extends React.Component {
    constructor() {
        super();
        this.state = {
            agencyName: '',
            externalUrl: '',
            postcode: '',
            recaptchaToken: ''
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    render() {
        return (
            <div>
                <form onSubmit={this.handleSubmit}>
                    <div className="form-group">
                        <label htmlFor="agencyName">Agency name</label>
                        <input className="form-control" name="agencyName" required onChange={this.handleChange} />
                    </div>
                    <div className="form-group">
                        <label htmlFor="externalUrl">Website URL</label>
                        <input className="form-control" name="externalUrl" onChange={this.handleChange} />
                    </div>
                    <div className="form-group">
                        <label htmlFor="externalUrl">Postcode</label>
                        <input className="form-control" name="postcode" onChange={this.handleChange} />
                    </div>
                    <button type="submit"
                            className="btn btn-primary g-recaptcha"
                            data-sitekey="reCAPTCHA_site_key"
                            data-callback="onSubmit"
                            data-action="submit">Create an agency
                    </button>
                </form>
            </div>
        );
    }

    handleSubmit() {
        fetch(`/api/verified/agency`, {
            method: 'POST',
            body: JSON.stringify(this.state),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(res => res.json())
            .then(data => console.log(data))
            .catch(err => console.error("Error:", err));
    }
}

ReactDOM.render(<CreateAgency />, document.getElementById('create-agency-root'));