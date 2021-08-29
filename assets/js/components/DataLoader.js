import React from 'react';
import LoadingInfo from "./LoadingInfo";

export default class DataLoader extends React.Component {

    state = {
        loadingInfo: {
            loaded: false,
            loading: false,
            loadingError: false,
            loadingErrorCode: null,
        },
    };

    constructor(props) {
        super(props);
        this.customFileNotFound = this.props.customFileNotFound;
        this.loadComponentData = this.props.loadComponentData;
    }

    componentDidMount() {
        this.fetchData();
    }

    componentDidUpdate(prevProps) {
        if (prevProps.url !== this.props.url) {
            this.fetchData();
        }
    }

    fetchData() {
        this.setState({loadingInfo: {loading: true}})

        fetch(this.props.url)
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
            .then(data => {
                this.setState({
                    loadingInfo: {
                        loading: false,
                        loaded: true
                    }
                })
                this.loadComponentData(data);
            });
    }

    render() {
        return (
            <LoadingInfo
                info={this.state.loadingInfo}
                customFileNotFound={this.props.customFileNotFound}
            />
        );
    }
}
