import React from 'react'
import ReactDOM from 'react-dom/client'
import Lanyard from './Lanyard'

const rootElement = document.getElementById('lanyard-root')
if (rootElement) {
  ReactDOM.createRoot(rootElement).render(
    <React.StrictMode>
      <Lanyard
        position={[0, 0, 20]}
        gravity={[0, -40, 0]}
        frontImage="uploads/ashish%20shoby.jpeg"
        imageFit="cover"
      />
    </React.StrictMode>
  )
}
