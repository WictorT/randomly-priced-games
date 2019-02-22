import React, {Component} from "react"
import {withStyles} from "@material-ui/core/styles"
import Card from "@material-ui/core/Card"
import CardContent from "@material-ui/core/CardContent"
import CardMedia from "@material-ui/core/CardMedia"
import Typography from "@material-ui/core/Typography"
import {fetchProductById} from "../../shared/store/actions/products"
import {connect} from "react-redux"
import Loader from "../../shared/componets/UI/Loader/Loader"

const styles = {
    cardMedia: {
        paddingTop: "56.25%", // 16:9
    },
    card: {
        marginTop: "50px",
        marginLeft: "10px",
        marginRight: "10px",
    },
    price: {
        marginTop: "10px"
    }
}

class ProductDetails extends Component {

    componentDidMount() {
        this.props.fetchProductById(this.props.match.params.id)
    }

    render() {
        const {classes, product} = this.props
        let loading = false

        if ( ! product ) {
            loading = true
        }

        return (
            <React.Fragment>
                {
                    loading
                        ? <Loader/>
                        : <Card className={classes.card}>

                            <CardMedia
                                className={classes.cardMedia}
                                image="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22288%22%20height%3D%22225%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20288%20225%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164edaf95ee%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164edaf95ee%22%3E%3Crect%20width%3D%22288%22%20height%3D%22225%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2296.32500076293945%22%20y%3D%22118.8%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" // eslint-disable-line max-len
                                title="Image title"
                            />
                            <CardContent>
                                <Typography gutterBottom variant="h5" component="h2">
                                    {product.name}
                                </Typography>
                                <Typography component="p">
                                    Lizards are a widespread group of squamate reptiles, with over 6,000 species, ranging
                                    across all continents except Antarctica
                                </Typography>
                                <Typography variant="button" className={classes.price}>
                                    {product.price}
                                </Typography>
                            </CardContent>
                        </Card>
                }
            </React.Fragment>
        )
    }
}

function mapStateToProps(state) {
    return {
        product: state.products.product,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        fetchProductById: id => dispatch(fetchProductById(id))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(withStyles(styles)(ProductDetails))
