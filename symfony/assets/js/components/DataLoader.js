import React from 'react';
import LoadingInfo from "./LoadingInfo";

class DataLoader extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            loadingInfo: {
                loaded: false,
                loading: false,
                loadingError: false,
                loadingErrorCode: null,
            },
        };

        this.loadComponentData = this.props.loadComponentData;
    }

    componentDidMount() {
        this.fetchData();
    }

    fetchData() {
        this.setState({loadingInfo: {loading: true}})

        let dataFetcher = this;
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

                console.log(data);

                dataFetcher.loadComponentData(data);
            });
    }

    render() {
        return (
            <LoadingInfo
                info={this.state.loadingInfo}
            />
        );
    }
}

export default DataLoader;