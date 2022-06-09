import React from 'react'

import '../../styles/map.scss'
import { MapContainer, Marker, Popup, TileLayer } from 'react-leaflet'

export default class Map extends React.Component {
  render () {
    return (
      <MapContainer center={[this.props.latitude, this.props.longitude]} zoom={13} scrollWheelZoom={false} className={this.props.className}>
        <TileLayer
          attribution='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>'
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        <Marker position={[this.props.latitude, this.props.longitude]}>
          <Popup>
            {this.props.addressLine1}
          </Popup>
        </Marker>
      </MapContainer>
    )
  }
}
